# News-aggregator-fullstack

## News aggregator build with Laravel,Mysql,React, and Docker-compose

## Basic Features
    ```bash
        
        1. User can sign up and sign in
        2. JWT Authentication with access token and refresh token
        3. User can set news preference from user setting.
        4. User can filter the news source
       
    ```
## Project Run Instruction

```bash

Step 1: git clone https://github.com/shaikhalamin/news-aggregator-fullstack.git

Step 2: cd news-aggregator-fullstack

Step 3: sudo chmod +x setup.sh

Step 4: ./setup.sh

```
## N:B: please be patient it will take 5-6 mins to setup docker env and project

```bash

If you find some terminal error after initial setup like migration setup error, please run below command to finish initial setup:

docker exec -it backend-container php artisan migrate:fresh

```

## N:B: No data will be displayed until we save news source preference from user setting

```bash

N:B: Due to API limitations of Nytimes_api and News_api_org, news data did not scrapped using any scheduled command.
However data scrapped, after saving preferred news source from user setting and finalized data scrapped using multiple queue setting from supervisor.
As News_api_org does not support category filtering directly, I have used each category as a topic in News_api_org to store news

```

## After all the setup done you can browse http://localhost:7890 to see the result
## Laravel backend will be running on http://localhost:9000


