import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:pull_to_refresh/pull_to_refresh.dart';
import 'package:aduanefie/application/home/home_notifier.dart';
import 'package:aduanefie/application/home/home_state.dart';
import 'package:aduanefie/infrastructure/services/services.dart';
import 'package:aduanefie/presentation/pages/home/home_one/widget/market_one_item.dart';
import 'package:aduanefie/presentation/pages/home/home_three/widgets/market_three_item.dart';
import 'package:aduanefie/presentation/pages/service/widgets/service_one_category.dart';
import 'package:aduanefie/presentation/theme/app_style.dart';
import '../../theme/color_set.dart';
import '../home/home_two/widget/market_two_item.dart';
import 'widgets/service_three_category.dart';
import 'widgets/service_two_category.dart';

import 'package:aduanefie/presentation/components/components.dart';

class FilterCategoryService extends StatelessWidget {
  final HomeState state;
  final HomeNotifier event;
  final int categoryIndex;
  final RefreshController farmController;
  final CustomColorSet colors;

  const FilterCategoryService({
    super.key,
    required this.state,
    required this.event,
    required this.categoryIndex,
    required this.farmController,
    required this.colors,
  });

  @override
  Widget build(BuildContext context) {
    return SmartRefresher(
      enablePullDown: true,
      enablePullUp: true,
      physics: const BouncingScrollPhysics(),
      controller: farmController,
      header: WaterDropMaterialHeader(
        distance: 160.h,
        backgroundColor: AppStyle.white,
        color: AppStyle.textGrey,
      ),
      onLoading: () {
        event.fetchFilterFarm(context, controller: farmController);
      },
      onRefresh: () {
        event.fetchFilterFarm(
          context,
          controller: farmController,
          isRefresh: true,
        );
      },
      child: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            12.verticalSpace,
            AppHelpers.getType() == 1
                ? ServiceOneCategory(
                    state: state,
                    event: event,
                    colors: colors,
                    categoryIndex: categoryIndex,
                  )
                : AppHelpers.getType() == 2
                ? ServiceTwoCategory(
                    state: state,
                    event: event,
                    categoryIndex: categoryIndex,
                    colors: colors,
                  )
                : AppHelpers.getType() == 3
                ? ServiceThreeCategory(
                    state: state,
                    event: event,
                    categoryIndex: categoryIndex,
                    colors: colors,
                  )
                : ServiceTwoCategory(
                    state: state,
                    event: event,
                    categoryIndex: categoryIndex,
                    colors: colors,
                  ),
            TitleAndIcon(
              title: AppHelpers.getTranslation(TrKeys.farms),
              rightTitle:
                  "${AppHelpers.getTranslation(TrKeys.found)} ${state.filterShops.length.toString()} ${AppHelpers.getTranslation(TrKeys.results)}",
            ),
            state.isSelectCategoryLoading == -1
                ? const Loading()
                : state.filterShops.isNotEmpty
                ? ListView.builder(
                    padding: REdgeInsets.symmetric(vertical: 12),
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    scrollDirection: Axis.vertical,
                    itemCount: state.filterShops.length,
                    itemBuilder: (context, index) => Padding(
                      padding: REdgeInsets.only(bottom: 12),
                      child: AppHelpers.getType() == 1
                          ? MarketOneItem(
                              shop: state.filterShops[index],
                              isSimpleShop: true,
                            )
                          : AppHelpers.getType() == 2
                          ? MarketTwoItem(
                              shop: state.filterShops[index],
                              isSimpleShop: true,
                              isFilter: true,
                            )
                          : AppHelpers.getType() == 3
                          ? MarketThreeItem(
                              shop: state.filterShops[index],
                              isSimpleShop: true,
                            )
                          : MarketItem(
                              shop: state.filterShops[index],
                              isSimpleShop: true,
                            ),
                    ),
                  )
                : Padding(
                    padding: EdgeInsets.only(top: 24.h),
                    child: _resultEmpty(),
                  ),
          ],
        ),
      ),
    );
  }
}

Widget _resultEmpty() {
  return Column(
    children: [
      Image.asset("assets/images/notFound.png"),
      Text(
        AppHelpers.getTranslation(TrKeys.nothingFound),
        style: AppStyle.interSemi(size: 18.sp),
      ),
      Padding(
        padding: EdgeInsets.symmetric(horizontal: 32.w),
        child: Text(
          AppHelpers.getTranslation(TrKeys.trySearchingAgain),
          style: AppStyle.interRegular(size: 14.sp),
          textAlign: TextAlign.center,
        ),
      ),
    ],
  );
}
