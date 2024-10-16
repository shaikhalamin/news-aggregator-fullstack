import _ from "lodash";
import {
  FilterType,
  KeyValueObject,
  NewsFeedQueryFilters,
} from "../types/feedtypes";
import qs from "qs";

export const removeFalsy = (
  malformedObject: KeyValueObject | NewsFeedQueryFilters
) => {
  return _.omitBy(malformedObject, _.isEmpty);
};

export const createFilterUrl = (filterObject: FilterType) => {
  const queryParams = removeFalsy(filterObject.filters as NewsFeedQueryFilters);
  const query = qs.stringify(
    {
      ...filterObject.basic,
      ...queryParams,
    },
    {
      encodeValuesOnly: true,
    }
  );

  return query;
};

export const formFieldsToKeyValue = (queryObject: KeyValueObject) => {
  return removeFalsy(queryObject as KeyValueObject);
};

export const generateQueryFilterUrl = (
  queryObject: KeyValueObject
): {
  filterUrl: string;
  queryParams: _.Dictionary<string | number | boolean>;
} => {
  const newQueryObject = { ...queryObject };
  const queryParams = removeFalsy(newQueryObject as KeyValueObject);
  const filterUrl = qs.stringify(
    {
      ...queryParams,
    },
    {
      encodeValuesOnly: true,
    }
  );
  return {
    filterUrl,
    queryParams,
  };
};
