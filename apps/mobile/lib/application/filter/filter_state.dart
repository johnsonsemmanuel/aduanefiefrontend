import 'package:flutter/material.dart';
import 'package:freezed_annotation/freezed_annotation.dart';
import 'package:aduanefie/infrastructure/models/data/filter_model.dart';
import 'package:aduanefie/infrastructure/models/data/take_data.dart';

import 'package:aduanefie/infrastructure/models/data/shop_data.dart';

part 'filter_state.freezed.dart';

@freezed
abstract class FilterState with _$FilterState {
  const factory FilterState({
    @Default(null) FilterModel? filterModel,
    @Default(false) bool freeDelivery,
    @Default(false) bool deals,
    @Default(true) bool open,
    @Default(0) int shopCount,
    @Default(100) double endPrice,
    @Default(1) double startPrice,
    @Default(false) bool isLoading,
    @Default(false) bool isTagLoading,
    @Default(true) bool isShopLoading,
    @Default(true) bool isFarmLoading,
    @Default(RangeValues(1, 100)) RangeValues rangeValues,
    @Default([]) List<ShopData> shops,
    @Default([]) List<TakeModel> tags,
    @Default([]) List<int> prices,
    @Default([]) List<ShopData> farm,
  }) = _FilterState;

  const FilterState._();
}
