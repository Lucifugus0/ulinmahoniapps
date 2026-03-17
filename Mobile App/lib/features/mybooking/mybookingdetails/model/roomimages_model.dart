class RoomImageModel {
  final int roomId;
  final String? imageShow; 
  final List<RoomImageDataModel>? images; 

  RoomImageModel({
    required this.roomId,
    this.imageShow,
    this.images,
  });

  factory RoomImageModel.fromJson(Map<String, dynamic> json) {
    String? validImageShow; 
    List<RoomImageDataModel>? parsedImages; 

    
    if (json['images'] is List) {
      final List<dynamic> imagesList = json['images'];
      parsedImages = imagesList
          .map((e) => RoomImageDataModel.fromJson(e as Map<String, dynamic>))
          .toList();

      
      for (var imageEntry in parsedImages) {
        String? currentImageShow = imageEntry.imageData;
        if (currentImageShow != null && currentImageShow.isNotEmpty) {
          validImageShow = currentImageShow;
          break; 
        }
      }
    }

    
    
    final int roomId = json['idrec'] ?? json['room_id'] ?? 0;

    return RoomImageModel(
      roomId: roomId,
      imageShow: validImageShow, 
      images: parsedImages, 
    );
  }
}

class RoomImageDataModel {
  final int id;
  final String? imageData; 

  RoomImageDataModel({
    required this.id,
    this.imageData,
  });

  factory RoomImageDataModel.fromJson(Map<String, dynamic> json) {
    return RoomImageDataModel(
      id: json['id'],
      imageData: json['image_data'],
    );
  }
}