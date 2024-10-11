export type FeedPreferancePayloadType = {
  source: string;
  metadata?: {
    categories?: string[];
    authors?: string[];
  };
};

export type FeedPreference = {
  id: number;
  source: string;
  metadata: {
    authors: string[];
    categories: string[];
  };
  user_id: number;
};
