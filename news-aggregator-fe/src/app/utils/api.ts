import _ from "lodash";
import { FilterType, KeyValueObject } from "../types/feedtypes";
import qs from "qs";

export const removeFalsy = (malformedObject: KeyValueObject) => {
  return _.omitBy(malformedObject, _.isEmpty);
};

const createFilterUrl = (filterObject: FilterType) => {
  const query = qs.stringify(
    {
      ...filterObject.basic,
      ...filterObject.order,
      ...removeFalsy(filterObject.filters as KeyValueObject),
    },
    {
      encodeValuesOnly: true,
    }
  );

  return query;
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
