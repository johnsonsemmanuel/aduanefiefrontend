import 'package:auto_route/auto_route.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:pull_to_refresh/pull_to_refresh.dart';
import 'package:aduanefie/application/filter/filter_provider.dart';
import 'package:aduanefie/infrastructure/services/services.dart';
import 'package:aduanefie/presentation/pages/home/home_one/widget/market_one_item.dart';
import 'package:aduanefie/presentation/pages/home/home_three/widgets/market_three_item.dart';

import 'package:aduanefie/application/filter/filter_notifier.dart';
import 'package:aduanefie/presentation/theme/app_style.dart';
import '../home_two/widget/market_two_item.dart';
import '../shimmer/all_shop_shimmer.dart';

import 'package:aduanefie/presentation/components/components.dart';

@RoutePage()
class ResultFilterPage extends ConsumerStatefulWidget {
  final int categoryId;

  const ResultFilterPage({super.key, required this.categoryId});

  @override
  ConsumerState<ResultFilterPage> createState() => _ResultFilterState();
}

class _ResultFilterState extends ConsumerState<ResultFilterPage> {
  late FilterNotifier event;
  final RefreshController _shopController = RefreshController();
  final RefreshController _farmController = RefreshController();

  @override
  void initState() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref
          .read(filterProvider.notifier)
          .fetchFarm(context, widget.categoryId);
    });
    super.initState();
  }

  @override
  void didChangeDependencies() {
    event = ref.read(filterProvider.notifier);
    super.didChangeDependencies();
  }

  @override
  void dispose() {
    _shopController.dispose();
    _farmController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(filterProvider);
    return Scaffold(
      body: Column(
        children: [
          CommonAppBar(
            child: Text(
              AppHelpers.getTranslation(TrKeys.shops),
              style: AppStyle.interNoSemi(size: 18.sp),
            ),
          ),
          Expanded(
            child: SmartRefresher(
              controller: _farmController,
              enablePullUp: true,
              enablePullDown: true,
              onLoading: () {
                event.fetchFarmPage(
                  context,
                  _farmController,
                  widget.categoryId,
                );
              },
              onRefresh: () {
                event.fetchFarmPage(
                  context,
                  _farmController,
                  widget.categoryId,
                  isRefresh: true,
                );
              },
              child: SingleChildScrollView(
                child: Column(
                  children: [
                    24.verticalSpace,
                    state.isFarmLoading
                        ? const AllShopShimmer()
                        : Column(
                            children: [
                              TitleAndIcon(
                                title: AppHelpers.getTranslation(
                                  TrKeys.allShops,
                                ),
                                rightTitle:
                                    "${AppHelpers.getTranslation(TrKeys.found)} ${state.farm.length.toString()} ${AppHelpers.getTranslation(TrKeys.results)}",
                              ),
                              ListView.builder(
                                padding: EdgeInsets.only(top: 6.h),
                                shrinkWrap: true,
                                physics: const NeverScrollableScrollPhysics(),
                                scrollDirection: Axis.vertical,
                                itemCount: state.farm.length,
                                itemBuilder: (context, index) =>
                                    AppHelpers.getType() == 0
                                    ? MarketItem(
                                        shop: state.farm[index],
                                        isSimpleShop: true,
                                      )
                                    : AppHelpers.getType() == 1
                                    ? MarketOneItem(
                                        shop: state.farm[index],
                                        isSimpleShop: true,
                                      )
                                    : AppHelpers.getType() == 2
                                    ? MarketTwoItem(
                                        shop: state.farm[index],
                                        isSimpleShop: true,
                                        isFilter: true,
                                      )
                                    : MarketThreeItem(
                                        shop: state.farm[index],
                                        isSimpleShop: true,
                                      ),
                              ),
                            ],
                          ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.startFloat,
      floatingActionButton: Padding(
        padding: EdgeInsets.only(left: 16.w),
        child: const PopButton(),
      ),
    );
  }
}
