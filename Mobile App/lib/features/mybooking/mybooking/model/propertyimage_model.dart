class PropertyImageModel {
  final int propertyId;
  final String? imageShow; 
  final List<ImageDataModel>? images; 

  PropertyImageModel({
    required this.propertyId,
    this.imageShow,
    this.images,
  });

  factory PropertyImageModel.fromJson(Map<String, dynamic> json) {
    String? validImageShow; 
    List<ImageDataModel>? parsedImages; 

    
    if (json['images'] is List) {
      final List<dynamic> imagesList = json['images'];
      parsedImages = imagesList
          .map((e) => ImageDataModel.fromJson(e as Map<String, dynamic>))
          .toList();

      for (var imageEntry in parsedImages) {
        String? currentImageShow = imageEntry.imageData;
        if (currentImageShow != null && currentImageShow.isNotEmpty) {
          validImageShow = currentImageShow;
          break; 
        }
      }
    }
    final int propertyId = json['idrec'] ?? 0; 

    return PropertyImageModel(
      propertyId: propertyId,
      imageShow: validImageShow, 
      images: parsedImages, 
    );
  }
}

class ImageDataModel {
  final int id;
  final String? imageData; 

  ImageDataModel({
    required this.id,
    this.imageData,
  });

  factory ImageDataModel.fromJson(Map<String, dynamic> json) {
    return ImageDataModel(
      id: json['id'],
      imageData: json['image_data'],
    );
  }
}