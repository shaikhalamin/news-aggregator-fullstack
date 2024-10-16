# News-aggregator-fullstack

## News aggregator build with Laravel,Mysql,React, and Docker-compose

## Basic Features
    ```bash
        
        1. User can sign up and sign in
        2. JWT Authentication with access token and refresh token
        3. User can set news preference from user setting with any of these APIs ==> ['GURDIAN_API', 'NYTIMES_API', 'NEWSAPI_ORG_API']
        4. User can filter the news source from home page by keyword, date range, category and source
       
    ```
## Project Run Instruction

```bash

Step 1: git clone https://github.com/shaikhalamin/news-aggregator-fullstack.git

Step 2: cd news-aggregator-fullstack

Step 3: sudo chmod +x setup.sh

Step 4: You may change [NEWSAPI_ORG-->GURDIAN_API-->NYTIMES_API] API keys from .env.example but default key will also works from setup

Step 5: ./setup.sh


```
## N:B: please be patient it will take 5-6 mins to setup docker env and project

```bash

If you find some terminal error after initial setup like migration setup error, please run below commands manually to finish initial setup:

docker exec -it backend-container composer install
docker exec -it backend-container php artisan migrate:fresh
docker exec -it backend-container php artisan key:generate
docker exec -it backend-container php artisan cache:clear
docker exec -it backend-container php artisan config:clear

```

## N:B: No data will be displayed until we save news source preference from user setting

# N:B: [Disclaimers] Scheduled command did not used for data scrapping for API limitations, Multiple queues used for category wise data fetching: 

```bash
1. Due to API limitations of Nytimes_api and News_api_org, news data did not scrapped using any scheduled command.
2. However data scrapped, after saving preferred news source from user setting and finalized data scrapped using multiple queue setting from supervisor.
3. For Gurdian API and Nytimes API last one year data will scrapped after saving any of these two category.
3. As News_api_org does not support category filtering directly, I have used each category as a topic in News_api_org to store news
4. For News_api_org Author Preference will not directly work for API limitations.

```

## N:B: Please keep your news source category list small due to developer API key restrictions otherwise it will always get 429 status for data scrapping.

## After all the setup done you can browse http://localhost:7890 to see the result

## How to use the system ?:

```bash

  1. Browse signup page to create account and then login to system.
  2. Browse setting page and set your news source setting
  3. Depending on the number of category selection, please wait few minutes until the data scrapped get completed by queues. 
  4. Now start browsing homepage to see the news and you can filter on that data

```

## Laravel backend will be running on http://localhost:9000


