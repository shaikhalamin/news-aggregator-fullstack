export type UserFeed = {
  id: number;
  title: string | null;
  description: string | null;
  content: string | null;
  content_html: string | null;
  image_url: string | null;
  author: string | null;
  news_url: string | null;
  news_api_url: null;
  source: string | null;
  response_source: string | null;
  is_topstories: number;
  category: string | null;
  published_at: string;
  user_id: number | null;
  created_at: string | null;
  updated_at: string | null;
};
