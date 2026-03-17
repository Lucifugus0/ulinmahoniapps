class AvailabilityCheckResponse {
  final String status;
  final AvailabilityData data;

  AvailabilityCheckResponse({
    required this.status,
    required this.data,
  });

  factory AvailabilityCheckResponse.fromJson(Map<String, dynamic> json) {
    return AvailabilityCheckResponse(
      status: json['status'],
      data: AvailabilityData.fromJson(json['data']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'status': status,
      'data': data.toJson(),
    };
  }
}

class AvailabilityData {
  final bool isAvailable;

  
  
  

  AvailabilityData({
    required this.isAvailable,
    
  });

  factory AvailabilityData.fromJson(Map<String, dynamic> json) {
    return AvailabilityData(
      isAvailable: json['is_available'] as bool, 
      
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'is_available': isAvailable,
      
    };
  }
}