import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';
import '../../../../searchresult/model/searchfilter_model.dart';
import '../../../../searchresult/provider/searchresult_provider.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';

class SearchFilterModal extends ConsumerStatefulWidget {
  const SearchFilterModal({super.key});

  @override
  ConsumerState<SearchFilterModal> createState() => _SearchFilterModalState();
}

class _SearchFilterModalState extends ConsumerState<SearchFilterModal> {
  String? selectedCategory;
  String? selectedRentType;
  int? durationRaw;
  DateTime? checkInDate;
  int? durationInDays;

  final TextEditingController _checkInController = TextEditingController();
  final TextEditingController _checkOutController = TextEditingController();
  final TextEditingController _durationController = TextEditingController();

  late AppLocalizations localizations;
  late Map<String, String> rentTypeDisplayToValue;
  late Map<String, String> rentTypeValueToDisplay;

  late Map<String, String> categoryDisplayToValue;
  late Map<String, String> categoryValueToDisplay;

  bool _filterApplied = false;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    localizations = AppLocalizations.of(context)!;

    rentTypeDisplayToValue = {
      localizations.filterRentTypeDaily: 'Daily',
      localizations.filterRentTypeMonthly: 'Monthly',
    };
    rentTypeValueToDisplay = {
      'Daily': localizations.filterRentTypeDaily,
      'Monthly': localizations.filterRentTypeMonthly,
    };

    categoryDisplayToValue = {
      localizations.filterCategoryKos: 'Kos',
      localizations.filterCategoryApartment: 'Apartment',
      localizations.filterCategoryHotel: 'Hotel',
      localizations.filterCategoryVilla: 'Villa',
    };
    categoryValueToDisplay = {
      'Kos': localizations.filterCategoryKos,
      'Apartment': localizations.filterCategoryApartment,
      'Hotel': localizations.filterCategoryHotel,
      'Villa': localizations.filterCategoryVilla,
    };

    final currentFilter = ref.read(searchFilterProvider);
    final List<String> categoryValues = ['Kos', 'Apartment', 'Hotel', 'Villa'];
    selectedCategory = currentFilter.category;
    if (selectedCategory != null && !categoryValues.contains(selectedCategory!)) {
      selectedCategory = null;
    }

    selectedRentType = currentFilter.rentType;
    final List<String> availableRentOptionsForCategory = getRentTypeOptions(selectedCategory)
        .map((e) => rentTypeDisplayToValue.entries
        .firstWhere((entry) => entry.value == e, orElse: () => MapEntry(e, e))
        .key)
        .toList();
    if (selectedRentType != null &&
        !availableRentOptionsForCategory.contains(selectedRentType!)) {
      selectedRentType = null;
    }

    if (currentFilter.checkInDate != null &&
        currentFilter.checkInDate!.isNotEmpty) {
      _checkInController.text = currentFilter.checkInDate!;
      try {
        checkInDate = DateFormat('dd-MM-yyyy')
            .parse(currentFilter.checkInDate!)
            .toLocal();
      } catch (e) {
        checkInDate = null;
      }
    } else {
      checkInDate = null;
      _checkInController.clear();
    }

    durationRaw = currentFilter.durationRaw ?? 1;
    _durationController.text = durationRaw?.toString() ?? '1';
    durationInDays = currentFilter.durationInDays;

    if (currentFilter.checkOutDate != null &&
        currentFilter.checkOutDate!.isNotEmpty) {
      _checkOutController.text = currentFilter.checkOutDate!;
    } else {
      _checkOutController.clear();
    }

    WidgetsBinding.instance.addPostFrameCallback((_) {
      // CRITICAL FIX: Check if widget is still mounted
      if (mounted) {
        _updateDurationAndCheckOutDate();
      }
    });
  }

  @override
  void initState() {
    super.initState();
  }

  @override
  void dispose() {
    _checkInController.dispose();
    _checkOutController.dispose();
    _durationController.dispose();
    super.dispose();
  }

  List<String> getRentTypeOptions(String? category) {
    if (category == 'Hotel' || category == 'Villa') {
      return ['Daily'];
    } else {
      return ['Monthly', 'Daily'];
    }
  }

  Future<void> _selectCheckInDate() async {
    final now = DateTime.now();
    final oneYearFromNow = DateTime(now.year + 1, now.month, now.day);

    DateTime? picked = await showDatePicker(
      context: context,
      initialDate: checkInDate ?? now,
      firstDate: now,
      lastDate: oneYearFromNow,
      builder: (BuildContext context, Widget? child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(
              primary: Color(0xFF005F21),
              onPrimary: Colors.white,
              surface: Colors.white,
              onSurface: Colors.black,
            ),
            dialogBackgroundColor: Colors.white,
          ),
          child: child!,
        );
      },
    );

    if (picked != null) {
      setState(() {
        checkInDate = DateTime(picked.year, picked.month, picked.day);
        _checkInController.text =
            DateFormat('dd-MM-yyyy').format(checkInDate!);
        _updateDurationAndCheckOutDate();
      });
    }
  }

  void _updateDurationAndCheckOutDate() {
    if (checkInDate != null &&
        durationRaw != null &&
        durationRaw! > 0 &&
        selectedRentType != null) {
      DateTime? calculatedCheckOutDate;

      if (selectedRentType == 'Daily') {
        durationInDays = durationRaw;
        calculatedCheckOutDate = checkInDate!.add(Duration(days: durationRaw!));
      } else if (selectedRentType == 'Monthly') {
        durationInDays = durationRaw! * 30;
        calculatedCheckOutDate = DateTime(
          checkInDate!.year,
          checkInDate!.month + durationRaw!,
          checkInDate!.day,
        );
      } else {
        durationInDays = null;
        calculatedCheckOutDate = null;
      }

      if (calculatedCheckOutDate != null) {
        _checkOutController.text =
            DateFormat('dd-MM-yyyy').format(calculatedCheckOutDate);
      } else {
        _checkOutController.clear();
      }
    } else {
      durationInDays = null;
      _checkOutController.clear();
    }
    setState(() {});
  }

  @override
  Widget build(BuildContext context) {
    final List<String> categoryValues = ['Kos', 'Apartment', 'Hotel', 'Villa'];
    final List<String> categoryOptions = categoryValues
        .map((value) => categoryValueToDisplay[value]!)
        .toList();

    final List<String> rentOptions = getRentTypeOptions(selectedCategory)
        .map((value) => rentTypeValueToDisplay[value]!)
        .toList();

    String? displaySelectedRentType;
    if (selectedRentType != null) {
      displaySelectedRentType = rentTypeValueToDisplay[selectedRentType!];
      if (displaySelectedRentType == null ||
          !rentOptions.contains(displaySelectedRentType!)) {
        displaySelectedRentType = null;
        selectedRentType = null;
        WidgetsBinding.instance.addPostFrameCallback((_) {
          // CRITICAL FIX: Check if widget is still mounted
          if (mounted) {
            _updateDurationAndCheckOutDate();
          }
        });
      }
    }

    String? displaySelectedCategory;
    if (selectedCategory != null) {
      displaySelectedCategory = categoryValueToDisplay[selectedCategory!];
    }

    return PopScope(
      canPop: true,
      onPopInvoked: (bool didPop) {
        if (didPop) {
          if (!_filterApplied) {
            ref.read(searchFilterProvider.notifier).resetFilter();
          }
        }
      },
      // PERUBAHAN UTAMA DI SINI: Menggunakan Dialog
      child: Dialog(
        insetPadding: const EdgeInsets.all(20), // Jarak dari tepi layar
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16), // Radius sudut untuk semua sisi
        ),
        backgroundColor: Colors.white,
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              mainAxisSize: MainAxisSize.min, // Agar tinggi dialog menyesuaikan konten
              children: [
                // Container abu-abu (handle) dihapus karena ini popup tengah
                Text(
                  localizations.filtertitle, // Judul Opsional
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 20),
                DropdownButtonFormField<String>(
                  dropdownColor: Colors.white,
                  value: displaySelectedCategory,
                  items: categoryOptions
                      .map((item) =>
                      DropdownMenuItem(value: item, child: Text(item)))
                      .toList(),
                  decoration: InputDecoration(
                      labelText: localizations.filterLabelCategory),
                  onChanged: (value) {
                    setState(() {
                      selectedCategory = categoryDisplayToValue[value!];
                      selectedRentType = null;
                      durationRaw = null;
                      durationInDays = null;
                      _checkInController.clear();
                      _checkOutController.clear();
                      _durationController.clear();
                      checkInDate = null;
                      _updateDurationAndCheckOutDate();
                    });
                  },
                ),
                const SizedBox(height: 12),
                DropdownButtonFormField<String>(
                  dropdownColor: Colors.white,
                  value: displaySelectedRentType,
                  items: rentOptions
                      .map((item) =>
                      DropdownMenuItem(value: item, child: Text(item)))
                      .toList(),
                  decoration: InputDecoration(
                      labelText: localizations.filterLabelRentType),
                  onChanged: rentOptions.isEmpty
                      ? null
                      : (displayValue) {
                    setState(() {
                      selectedRentType =
                      rentTypeDisplayToValue[displayValue!];
                      _updateDurationAndCheckOutDate();
                    });
                  },
                ),
                const SizedBox(height: 12),
                TextField(
                  controller: _checkInController,
                  readOnly: true,
                  onTap: _selectCheckInDate,
                  decoration: InputDecoration(
                    labelText: localizations.filterLabelCheckIn,
                    hintText: localizations.filterHintCheckIn,
                    suffixIcon: const Icon(Icons.calendar_today),
                  ),
                ),
                const SizedBox(height: 12),
                Container(
                  decoration: BoxDecoration(
                    border: Border(
                      bottom: BorderSide(color: Colors.grey.shade300),
                    ),
                  ),
                  child: Row(
                    children: [
                      Expanded(
                        child: Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                          child: Text(
                            (durationRaw ?? 1).toString(),
                            textAlign: TextAlign.left,
                            style: const TextStyle(
                              fontSize: 14,
                              color: Colors.black87,
                            ),
                          ),
                        ),
                      ),
                      Row(
                        children: [
                          IconButton(
                            onPressed: () {
                              final currentValue = durationRaw ?? 1;
                              if (currentValue > 1) {
                                setState(() {
                                  durationRaw = currentValue - 1;
                                  _durationController.text = durationRaw.toString();
                                  _updateDurationAndCheckOutDate();
                                });
                              }
                            },
                            icon: const Icon(Icons.remove),
                            color: AppColors.primaryColor,
                            iconSize: 20,
                          ),
                          Container(
                            width: 1,
                            height: 24,
                            color: Colors.grey.shade300,
                          ),
                          IconButton(
                            onPressed: () {
                              final currentValue = durationRaw ?? 1;
                              final maxValue = selectedRentType == 'Monthly' ? 12 : 31;
                              if (currentValue < maxValue) {
                                setState(() {
                                  durationRaw = currentValue + 1;
                                  _durationController.text = durationRaw.toString();
                                  _updateDurationAndCheckOutDate();
                                });
                              }
                            },
                            icon: const Icon(Icons.add),
                            color: AppColors.primaryColor,
                            iconSize: 20,
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 12),
                TextField(
                  controller: _checkOutController,
                  enabled: false,
                  decoration: InputDecoration(
                    labelText: localizations.filterLabelCheckOut,
                    hintText: localizations.filterHintCheckOut,
                    suffixIcon: const Icon(Icons.calendar_today),
                  ),
                ),
                const SizedBox(height: 24),
                ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    minimumSize: const Size.fromHeight(50),
                    backgroundColor: AppColors.primaryColor,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                  ),
                  onPressed: () {
                    final newFilter = SearchFilter(
                      category: selectedCategory,
                      rentType: selectedRentType,
                      checkInDate: _checkInController.text.isNotEmpty
                          ? _checkInController.text
                          : null,
                      durationRaw: durationRaw,
                      durationInDays: durationInDays,
                      checkOutDate: _checkOutController.text.isNotEmpty
                          ? _checkOutController.text
                          : null,
                      city: null,
                      province: null,
                    );

                    _filterApplied = true;
                    ref
                        .read(searchFilterProvider.notifier)
                        .updateFilter(newFilter);
                    ref.invalidate(searchResultsProvider);

                    context.pop();
                    context.push('/search');
                  },
                  child: Text(
                    localizations.filterButtonSearch,
                    style: const TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 16,
                        color: Colors.white),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}