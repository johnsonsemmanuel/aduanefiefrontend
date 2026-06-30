import 'package:aduanefie/infrastructure/models/models.dart';
import 'package:aduanefie/domain/handlers/handlers.dart';

abstract class CurrenciesRepositoryFacade {
  Future<ApiResult<CurrenciesResponse>> getCurrencies();
}
