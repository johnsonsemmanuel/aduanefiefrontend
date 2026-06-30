import 'package:freezed_annotation/freezed_annotation.dart';
import 'package:aduanefie/infrastructure/models/models.dart';

part 'home_state.freezed.dart';

@freezed
abstract class HomeState with _$HomeState {
  const factory HomeState({
    @Default(true) bool isCategoryLoading,
    @Default(true) bool isBannerLoading,
    @Default(true) bool isShopLoading,
    @Default(true) bool isFarmLoading,
    @Default(true) bool isFarmNewLoading,
    @Default(true) bool isStoryLoading,
    @Default(true) bool isShopRecommendLoading,
    @Default(-1) int totalShops,
    @Default(-1) int selectIndexCategory,
    @Default(-1) int selectIndexSubCategory,
    @Default(0) int isSelectCategoryLoading,
    @Default(null) AddressNewModel? addressData,
    @Default([]) List<CategoryData> categories,
    @Default([]) List<BannerData> banners,
    @Default([]) List<BannerData> ads,
    @Default(null) BannerData? banner,
    @Default([]) List<ShopData> shops,
    @Default([]) List<ShopData> farm,
    @Default([]) List<ShopData> newFarm,
    @Default([]) List<List<StoryModel?>?>? story,
    @Default([]) List<ShopData> shopsRecommend,
    @Default([]) List<ShopData> filterShops,
    @Default([]) List<ShopData> filterMarket,
  }) = _HomeState;

  const HomeState._();
}
