import 'package:aduanefie/infrastructure/models/models.dart';
import 'package:aduanefie/domain/handlers/handlers.dart';

abstract class PaymentsRepositoryFacade {
  Future<ApiResult<PaymentsResponse?>> getPayments();

  Future<ApiResult<TransactionsResponse>> createTransaction({
    required int orderId,
    required int paymentId,
  });
}
