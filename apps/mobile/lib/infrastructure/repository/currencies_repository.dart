import 'package:flutter/material.dart';
import 'package:aduanefie/domain/di/dependency_manager.dart';
import 'package:aduanefie/domain/interface/currencies.dart';
import 'package:aduanefie/infrastructure/models/models.dart';
import 'package:aduanefie/domain/handlers/handlers.dart';
import 'package:aduanefie/infrastructure/services/services.dart';

class CurrenciesRepository implements CurrenciesRepositoryFacade {
  @override
  Future<ApiResult<CurrenciesResponse>> getCurrencies() async {
    try {
      final client = dioHttp.client(requireAuth: false);
      final response = await client.get('/api/v1/rest/currencies/active');
      return ApiResult.success(
        data: CurrenciesResponse.fromJson(response.data),
      );
    } catch (e) {
      debugPrint('==> get currencies failure: $e');
      return ApiResult.failure(
        error: AppHelpers.errorHandler(e),
        statusCode: NetworkExceptions.getDioStatus(e),
      );
    }
  }
}
