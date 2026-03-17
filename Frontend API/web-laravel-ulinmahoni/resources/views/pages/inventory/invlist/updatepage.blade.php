<x-app-layout background="bg-white">
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">
                   Update Inventory List 📋
                </h1>
            </div>
        </div>

        <!-- form -->
        <form method="post" class="form_inv_update" enctype="multipart/form-data" action="{{ route('invlist.update', ['code' => $dataInvList->code]) }}">
            @csrf
            <div class="grid md:grid-cols-3 gap-3 mt-3">
                <div>
                    <label class="block text-sm font-medium mb-1" for="file">Upload Image 1:</label>
                    <input name="photo1" class="form-input w-full md:w-3/4 px-2 py-1" type="file" accept="image/jpeg" onchange="loadFileMultiple(event, 'output1-${code}')" required />
                    <img id="output1-${code}" style="max-width: 300px; max-height: 150px" class="mt-2 ml-3"/>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" for="file">Upload Image 2:</label>
                    <input name="photo2" class="form-input w-full md:w-3/4 px-2 py-1" type="file" accept="image/jpeg" onchange="loadFileMultiple(event, 'output2-${code}')" required />
                    <img id="output2-${code}" style="max-width: 300px; max-height: 150px" class="mt-2 ml-3"/>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" for="file">Upload Image 3:</label>
                    <input name="photo3" class="form-input w-full md:w-3/4 px-2 py-1" type="file" accept="image/jpeg" onchange="loadFileMultiple(event, 'output3-${code}')" required />
                    <img id="output3-${code}" style="max-width: 300px; max-height: 150px" class="mt-3 ml-3"/>
                </div>
            </div>
            <center><button class="btn bg-indigo-500 hover:bg-indigo-600 text-white mt-5" type="submit">
                <span class="xs:block ml-5 mr-5">Update Product</span>
            </button></center>
        </form>
    </div>
    </div>

    @section('js-page')
    <script>

    </script>
    @endsection
</x-app-layout>