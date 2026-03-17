<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use App\Models\UserAccessLevel;
use App\Models\DashboardWidget;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 8); // Default to 10 if not specified

        $users = User::with('role')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhereHas('role', function ($roleQuery) use ($search) {
                            $roleQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends([
                'search' => $search,
                'per_page' => $perPage
            ]);

        $roles = Role::where('name', '!=', 'Admin')->get();

        return view('pages/settings/users-management', [
            'users' => $users,
            'roles' => $roles,
            'perPage' => $perPage
        ]);
    }

    public function show()
    {
        $user = Auth::user();

        $roles = Role::where('name', '!=', 'Admin')->get();

        return view('pages.settings.user-account-settings', compact('user', 'roles'));
    }

    public function indexNew(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 8);
        $statusFilter = $request->input('status', '1'); // Default menampilkan hanya yang aktif

        $users = User::with(['role', 'property'])
            ->when($statusFilter !== 'all', function ($query) use ($statusFilter) {
                return $query->where('status', $statusFilter);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                        ->orWhere('username', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhereHas('role', function ($roleQuery) use ($search) {
                            $roleQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends([
                'search' => $search,
                'per_page' => $perPage,
                'status' => $statusFilter
            ]);

        $roles = Role::where('name', '!=', 'Admin')->where('id', '!=', 165)->get();
        $properties = \App\Models\Property::all();

        return view('pages.settings.users-management-new', compact('users', 'roles', 'perPage', 'properties', 'statusFilter'));
    }


    public function checkEmail(Request $request)
    {
        $emailExists = User::where('email', $request->email)->exists();

        return response()->json(['exists' => $emailExists]);
    }

    /**
     * Search users (AJAX endpoint) for users-management-new
     * Returns HTML for AJAX table refresh
     */
    public function searchUsers(Request $request)
    {
        try {
            $search = $request->input('search');
            $perPage = $request->input('per_page', 8);
            $statusFilter = $request->input('status', '1');

            $users = User::with(['role', 'property'])
                ->when($statusFilter !== 'all', function ($query) use ($statusFilter) {
                    return $query->where('status', $statusFilter);
                })
                ->when($search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%')
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                            ->orWhere('username', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhereHas('role', function ($roleQuery) use ($search) {
                                $roleQuery->where('name', 'like', '%' . $search . '%');
                            });
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Render pagination safely
            $paginationHtml = '';
            if ($users->hasPages()) {
                $paginationHtml = $users->links()->toHtml();
            }

            return response()->json([
                'html' => view('pages.settings.partials.users-table', [
                    'users' => $users,
                ])->render(),
                'pagination' => $paginationHtml,
                'debug' => [
                    'search' => $search,
                    'status' => $statusFilter,
                    'per_page' => $perPage,
                    'total_users' => $users->total(),
                    'current_count' => $users->count(),
                    'timestamp' => now()->toDateTimeString(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function store(Request $request)
    {
        // Validasi dasar
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'username'   => 'required|string|max:255|unique:users,username',
            'email'      => 'required|string|email|max:255|unique:users,email',
            'nik'        => 'nullable|string|max:20',
            'password'   => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).+$/'
            ],
            'role'       => 'required|exists:m_roles,id',
            'user_type'  => 'required|in:0,1',
        ];

        // Conditional validation: property_id required jika user_type = 1 (Site)
        if ($request->user_type == 1) {
            $rules['property_id'] = 'required|exists:m_properties,idrec';
        } else {
            $rules['property_id'] = 'nullable|exists:m_properties,idrec';
        }

        $validatedData = $request->validate($rules, [
            'password.confirmed' => 'Password and Confirm Password must match.',
            'password.regex'     => 'Password must contain at least 1 uppercase, 1 lowercase, 1 number, and 1 symbol.',
            'property_id.required' => 'Property is required for Site account type.',
        ]);

        // Simpan data user baru
        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name'  => $validatedData['last_name'],
            'username'   => $validatedData['username'],
            'email'      => $validatedData['email'],
            'nik'        => $validatedData['nik'] ?? null,
            'password'   => Hash::make($validatedData['password']),
            'is_admin'   => 1,
            'role_id'    => $validatedData['role'],
            'user_type'  => $validatedData['user_type'],
            'property_id' => $validatedData['user_type'] == 1 ? $validatedData['property_id'] : null,
            'status'     => 1, // default active
            'created_by' => Auth::id(),
            'created_at' => Carbon::now(),
        ]);

        // Tambahkan permission ID 1 dan 2 untuk user baru
        $defaultPermissions = array_unique([1, 2]); // Ensure uniqueness

        DB::transaction(function () use ($user, $defaultPermissions) {
            foreach ($defaultPermissions as $permissionId) {
                // Check if permission already exists
                $existingPermission = DB::table('role_permission')
                    ->where('user_id', $user->id)
                    ->where('permission_id', $permissionId)
                    ->exists();

                if (!$existingPermission) {
                    DB::table('role_permission')->insert([
                        'user_id' => $user->id,
                        'permission_id' => $permissionId,
                        'created_at' => Carbon::now(),
                        'created_by' => Auth::id(),
                    ]);
                }
            }
        });

        return redirect()->route('users-newManagement')
            ->with('success', 'User successfully created.');
    }



    public function indexEdit(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10); // Default to 10 if not specified

        $users = User::with('role')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhereHas('role', function ($roleQuery) use ($search) {
                            $roleQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends([
                'search' => $search,
                'per_page' => $perPage
            ]);

        $roles = Role::where('name', '!=', 'Admin')->get();

        $departments = DB::table('m_department')->where('pid', 0)->get();

        return view('pages.settings.users-management-edit', compact('users', 'roles', 'perPage', 'departments'));
    }

    public function updateUsers(Request $request, $id)
    {
        // Temukan user
        $user = User::find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User Not Found');
        }

        // Validasi input - sesuaikan dengan field yang ada di form
        $validationRules = [
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username,' . $id,
            'email'        => 'required|email|unique:users,email,' . $id,
            'phone_number' => 'nullable|string|max:20',
            'nik'          => 'nullable|string|max:20',
        ];

        // Jika ada field role dan status (untuk admin management)
        if ($request->has('role')) {
            $validationRules['role'] = 'required|exists:m_roles,id';
        }
        if ($request->has('status')) {
            $validationRules['status'] = 'required|in:0,1';
        }
        if ($request->has('user_type')) {
            $validationRules['user_type'] = 'required|in:0,1';
        }

        // Conditional validation untuk property_id based on user_type
        if ($request->has('property_id')) {
            if ($request->user_type == 1) {
                $validationRules['property_id'] = 'required|exists:m_properties,idrec';
            } else {
                $validationRules['property_id'] = 'nullable|exists:m_properties,idrec';
            }
        }

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validation Error: ' . $validator->errors()->first());
        }

        // Prepare update data
        $updateData = [
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'username'     => $request->username,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'nik'          => $request->nik,
            'updated_by'   => Auth::id(),
            'updated_at'   => now()
        ];

        // Update role jika ada (untuk admin management)
        if ($request->has('role')) {
            $updateData['role_id'] = $request->role;
        }

        // Update user_type jika ada
        if ($request->has('user_type')) {
            $updateData['user_type'] = $request->user_type;
        }

        // Update property_id jika ada
        if ($request->has('property_id')) {
            // Jika user_type = 0 (HO), set property_id ke null
            $updateData['property_id'] = $request->user_type == 1 ? $request->property_id : null;
        }

        // Update status jika ada (untuk admin management)
        if ($request->has('status')) {
            $updateData['status'] = $request->status;
        }

        // Update password jika ada
        if (!empty($request->password)) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Update user
        $user->update($updateData);

        // Jika update dari Account Settings (user sendiri), redirect kembali ke account settings
        // Jika update dari User Management (admin), redirect ke user management
        if ($user->id === Auth::id()) {
            return redirect()->route('users.show')->with('success', 'Profile updated successfully!');
        }

        return redirect()->route('users-newManagement')->with('success', 'User Updated Successfully!');
    }



    public function indexDelete(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10); // Default to 10 if not specified

        $users = User::with('role')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhereHas('role', function ($roleQuery) use ($search) {
                            $roleQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends([
                'search' => $search,
                'per_page' => $perPage
            ]);

        $roles = Role::where('name', '!=', 'Admin')->get();

        return view('pages/settings/users-management-delete', [
            'users' => $users,
            'roles' => $roles,
            'perPage' => $perPage
        ]);
    }

    public function deactivateUser($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->update([
                'status' => 'Inactive',
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'toast' => [
                    'type' => 'success',
                    'message' => 'User Deactivated',
                    'details' => 'User has been deactivated successfully'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'toast' => [
                    'type' => 'error',
                    'message' => 'Error',
                    'details' => 'Failed to deactivate user'
                ],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function indexUserAccessManagement()
    {
        $users = User::all();
        $permissions = Permission::all();
        $sidebarItems = DB::table('sidebar_items')
            ->select('id', 'name', 'parent_id', 'route', 'permission_id')
            ->get();

        return view(
            'pages/settings/user-access-management',
            compact('users', 'permissions', 'sidebarItems')
        );
    }

    public function getUserPermissions($userId)
    {
        $userPermissions = DB::table('role_permission')
            ->where('user_id', $userId)
            ->pluck('permission_id')
            ->toArray();

        return response()->json(['permissions' => $userPermissions]);
    }

    public function update(Request $request, $userId)
    {
        try {
            // Use transaction to ensure atomicity
            DB::transaction(function () use ($request, $userId) {
                // Clear existing permissions
                DB::table('role_permission')->where('user_id', $userId)->delete();

                // Insert new permissions
                if ($request->has('permissions') && is_array($request->permissions)) {
                    // Remove duplicate permission IDs
                    $uniquePermissions = array_unique($request->permissions);

                    foreach ($uniquePermissions as $permissionId) {
                        // Double check to prevent duplicate entry
                        $exists = DB::table('role_permission')
                            ->where('user_id', $userId)
                            ->where('permission_id', $permissionId)
                            ->exists();

                        if (!$exists) {
                            DB::table('role_permission')->insert([
                                'user_id' => $userId,
                                'permission_id' => $permissionId,
                                'created_at' => Carbon::now(),
                                'created_by' => Auth::id(),
                            ]);
                        }
                    }
                }
            });

            return response()->json(['message' => 'Permissions updated successfully!']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function accountGetData()
    {
        $users = User::select(['id', 'name', 'email', 'role'])->get();

        return response()->json($users);
    }

    public function userGetData()
    {
        $users = User::select(['id', 'name', 'email', 'role'])->get();

        return response()->json($users);
    }

    public function usergetMainMenus()
    {
        $mainMenus = Menu::select('id', 'header_menu')->distinct()->get();
        return response()->json($mainMenus);
    }

    // Method to get all menus
    public function getData(Request $request)
    {
        // Fetch all menus from m_menus
        $menus = Menu::whereNotNull('header_menu')
            ->whereNotNull('sub_menu')
            ->whereNotNull('menu')
            ->whereNotNull('kode')
            ->get();

        return response()->json($menus);
    }

    // Method to check user access for a specific menu
    public function getUserAccess(Request $request)
    {
        $userId = $request->query('user_id');
        $menuId = $request->query('menu_id');

        // Check if the user has access to the specified menu
        $hasAccess = UserAccessLevel::where('iduser', $userId)
            ->where('kode_menu', $menuId)
            ->exists();

        return response()->json(['hasAccess' => $hasAccess]);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|boolean'
            ]);

            $user = User::findOrFail($id);
            $user->update([
                'status' => $request->status,
                'updated_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    /**
     * Display master role management page
     */
    public function indexMasterRole(Request $request)
    {
        $perPage = $request->input('per_page', 8);
        $search = $request->input('search');

        // Get only users with is_admin = 1 and status = 1
        if ($perPage === 'all') {
            $adminUsers = User::with('role') 
                ->where('status', 1)
                ->when($search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%')
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                            ->orWhere('username', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhereHas('role', function ($roleQuery) use ($search) {
                                $roleQuery->where('name', 'like', '%' . $search . '%');
                            });
                    });
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $adminUsers = User::with('role')
                ->where('status', 1)
                ->when($search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%')
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                            ->orWhere('username', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhereHas('role', function ($roleQuery) use ($search) {
                                $roleQuery->where('name', 'like', '%' . $search . '%');
                            });
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->appends([
                    'per_page' => $perPage,
                    'search' => $search
                ]);
        }

        $roles = Role::all();

        // Get sidebar items for permissions
        $sidebarItems = DB::table('sidebar_items')
            ->select('id', 'name', 'parent_id', 'route', 'permission_id')
            ->get();

        // Get dashboard widgets for role-based widget management
        $dashboardWidgets = DashboardWidget::where('is_active', 1)
            ->orderBy('category')
            ->orderBy('order')
            ->get();

        return view('pages.settings.master-role-management', compact('adminUsers', 'roles', 'sidebarItems', 'dashboardWidgets', 'perPage'));
    }

    /**
     * Search admin users (AJAX endpoint)
     * Returns HTML for AJAX table refresh
     */
    public function searchMasterRoleUsers(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 8);
            $search = $request->input('search');

            // Get only users with status = 1
            if ($perPage === 'all') {
                $adminUsers = User::with('role')
                    ->where('status', 1)
                    ->when($search, function ($query, $search) {
                        return $query->where(function ($q) use ($search) {
                            $q->where('first_name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%')
                                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                                ->orWhere('username', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%')
                                ->orWhereHas('role', function ($roleQuery) use ($search) {
                                    $roleQuery->where('name', 'like', '%' . $search . '%');
                                });
                        });
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();

                return response()->json([
                    'html' => view('pages.settings.partials.master-role-table', [
                        'adminUsers' => $adminUsers,
                        'perPage' => 'all',
                    ])->render(),
                    'pagination' => '',
                ]);
            } else {
                $adminUsers = User::with('role')
                    ->where('status', 1)
                    ->when($search, function ($query, $search) {
                        return $query->where(function ($q) use ($search) {
                            $q->where('first_name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%')
                                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                                ->orWhere('username', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%')
                                ->orWhereHas('role', function ($roleQuery) use ($search) {
                                    $roleQuery->where('name', 'like', '%' . $search . '%');
                                });
                        });
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);

                // Render pagination safely
                $paginationHtml = '';
                if ($adminUsers->hasPages()) {
                    $paginationHtml = $adminUsers->links()->toHtml();
                }

                return response()->json([
                    'html' => view('pages.settings.partials.master-role-table', [
                        'adminUsers' => $adminUsers,
                        'perPage' => $perPage,
                    ])->render(),
                    'pagination' => $paginationHtml,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update user role
     */
    public function updateMasterRole(Request $request, $userId)
    {
        try {
            $request->validate([
                'role_id' => 'required|exists:m_roles,id'
            ]);

            $user = User::findOrFail($userId);

            // // Check if user is admin
            // if ($user->is_admin != 1) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'User is not an admin user'
            //     ], 403);
            // }

            $user->update([
                'role_id' => $request->role_id,
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user permissions for master role management
     */
    public function getMasterRolePermissions($userId)
    {
        try {
            $user = User::findOrFail($userId);

            // // Check if user is admin
            // if ($user->is_admin != 1) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'User is not an admin user',
            //         'permissions' => []
            //     ], 403);
            // }

            $userPermissions = DB::table('role_permission')
                ->where('user_id', $userId)
                ->pluck('permission_id')
                ->toArray();

            return response()->json([
                'success' => true,
                'permissions' => $userPermissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch permissions',
                'permissions' => []
            ], 500);
        }
    }

    /**
     * Update user permissions for master role management
     */
    public function updateMasterRolePermissions(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);

            // // Check if user is admin
            // if ($user->is_admin != 1) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'User is not an admin user'
            //     ], 403);
            // }

            // Use transaction to ensure atomicity
            DB::transaction(function () use ($request, $userId) {
                // Clear existing permissions
                DB::table('role_permission')->where('user_id', $userId)->delete();

                // Insert new permissions
                if ($request->has('permissions') && is_array($request->permissions)) {
                    // Remove duplicate permission IDs
                    $uniquePermissions = array_unique($request->permissions);

                    foreach ($uniquePermissions as $permissionId) {
                        // Double check to prevent duplicate entry
                        $exists = DB::table('role_permission')
                            ->where('user_id', $userId)
                            ->where('permission_id', $permissionId)
                            ->exists();

                        if (!$exists) {
                            DB::table('role_permission')->insert([
                                'user_id' => $userId,
                                'permission_id' => $permissionId,
                                'created_at' => Carbon::now(),
                                'created_by' => Auth::id(),
                            ]);
                        }
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Access rights updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new role
     */
    public function createRole(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:m_roles,name'
            ], [
                'name.required' => 'Role name is required',
                'name.unique' => 'Role name already exists'
            ]);

            $role = Role::create([
                'name' => $request->name,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully!',
                'role' => $role
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all dashboard widgets grouped by category
     */
    public function getDashboardWidgets()
    {
        try {
            $widgets = DashboardWidget::where('is_active', 1)
                ->orderBy('category')
                ->orderBy('order')
                ->get()
                ->groupBy('category');

            return response()->json([
                'success' => true,
                'widgets' => $widgets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch widgets: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard widgets assigned to a specific role
     */
    public function getRoleDashboardWidgets($roleId)
    {
        try {
            $role = Role::findOrFail($roleId);

            $assignedWidgetIds = DB::table('role_dashboard_widgets')
                ->where('role_id', $roleId)
                ->pluck('widget_id')
                ->toArray();

            return response()->json([
                'success' => true,
                'role' => $role,
                'widget_ids' => $assignedWidgetIds
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch role widgets: ' . $e->getMessage(),
                'widget_ids' => []
            ], 500);
        }
    }

    /**
     * Update dashboard widgets for a specific role
     */
    public function updateRoleDashboardWidgets(Request $request, $roleId)
    {
        try {
            $role = Role::findOrFail($roleId);

            // Clear existing widget assignments for this role
            DB::table('role_dashboard_widgets')->where('role_id', $roleId)->delete();

            // Insert new widget assignments
            if ($request->has('widget_ids') && is_array($request->widget_ids)) {
                foreach ($request->widget_ids as $widgetId) {
                    DB::table('role_dashboard_widgets')->insert([
                        'role_id' => $roleId,
                        'widget_id' => $widgetId,
                        'created_at' => Carbon::now(),
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Dashboard widgets updated successfully for role: ' . $role->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update widgets: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset user password (Superadmin only)
     */
    public function resetUserPassword(Request $request, $id)
    {
        // Check if current user is superadmin
        if (!Auth::user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized access. Only superadmin can reset passwords.');
        }

        // Find user
        $user = User::find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Prevent superadmin from resetting their own password via this method
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot reset your own password using this method. Please use the account settings.');
        }

        // Validate new password
        $validatedData = $request->validate([
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).+$/'
            ],
        ], [
            'new_password.confirmed' => 'Password and Confirm Password must match.',
            'new_password.regex'     => 'Password must contain at least 1 uppercase, 1 lowercase, 1 number, and 1 symbol.',
            'new_password.min'       => 'Password must be at least 8 characters.',
        ]);

        // Update password
        $user->update([
            'password' => Hash::make($validatedData['new_password']),
            'updated_by' => Auth::id(),
            'updated_at' => now()
        ]);

        return redirect()->route('users-newManagement')
            ->with('success', "Password for user '{$user->first_name} {$user->last_name}' has been reset successfully!");
    }
}
