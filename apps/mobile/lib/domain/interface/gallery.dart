import 'package:aduanefie/infrastructure/models/models.dart';
import 'package:aduanefie/infrastructure/models/response/multi_gallery_upload_response.dart';
import 'package:aduanefie/infrastructure/services/services.dart';
import 'package:aduanefie/domain/handlers/handlers.dart';

abstract class GalleryRepositoryFacade {
  Future<ApiResult<GalleryUploadResponse>> uploadImage(
    String file,
    UploadType uploadType,
  );

  Future<ApiResult<MultiGalleryUploadResponse>> uploadMultiImage(
    List<String?> filePaths,
    UploadType uploadType,
  );
}
