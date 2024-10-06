export type BasicType = {
  page: number;
  perPage: number;
};

export type KeyValueObject = {
  [key: string]: string | number | boolean;
};

export type NewsFeedQueryFilters = {
  q?: string;
  startDate?: string;
  endDate?: string;
  source?: string;
  category?: string;
};

export type FilterType = {
  basic: BasicType;
  order?: KeyValueObject;
  filters?: NewsFeedQueryFilters;
};

