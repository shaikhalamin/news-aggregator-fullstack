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
  basic: {
    page: number;
    per_page: number;
  };
  filters?: NewsFeedQueryFilters;
};

export const FilterTypeInitialVal: FilterType = {
  basic: { page: 1, per_page: 15 },
  filters: {
    q: "",
    startDate: "",
    endDate: "",
    source: "",
    category: "",
  },
};
