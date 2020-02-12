<?php
    require __DIR__ . '/vendor/autoload.php';

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    $stories_to_display = array();
    $url = "https://hn.algolia.com/api/v1/search_by_date?tags=front_page";
    $response = \Httpful\Request::get($url)
            ->send();
    $topStoriesArr = (array) $response->body->hits;
    if (count($topStoriesArr) > 0) {
        $topTenStories = array_slice($topStoriesArr, 10);
        foreach ($topTenStories as $index => $storyDetails) {
            $storyDetails = (array) $storyDetails;
            $date = explode('T', $storyDetails['created_at']);
            $stories_to_display[$index+1] = array(
                'id' => $storyDetails['objectID'],
                'title' => $storyDetails['title'],
                'url' => $storyDetails['url'],
                'comment_count' => $storyDetails['num_comments'],
                'score' => $storyDetails['points'],
                'date' => $date[0],
                'posted_by' => $storyDetails['author']
            );
        }   
    }

    echo $twig->render('index.html', ['stories_to_display' => $stories_to_display]);