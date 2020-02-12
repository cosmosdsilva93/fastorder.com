<?php
    require __DIR__ . '/vendor/autoload.php';

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    $tvshows_to_display = array();
    $today = date("Y-m-d");
    // echo $today;
    $url = "https://api.tvmaze.com/schedule?country=US&date=" . $today;
    $response = \Httpful\Request::get($url)
            ->send();
    $tvShowsArr = (array) $response->body;
    if (count($tvShowsArr) > 0) {
        $topTenShows = array_slice($tvShowsArr, -15, 15, true);
        krsort($topTenShows);
        foreach ($topTenShows as $showData) {
            $showData = (array) $showData;
            $showMetaData = (array) $showData['show'];
            $name = $showMetaData['name'];
            $name .= ($showData['season'] != date('Y')) ? " - " . $showData['season'] . "x" . $showData['number'] : "";
            $tvshows_to_display[] = array(
                'id' => $showData['id'],
                'date' => $showData['airdate'],
                'time' => $showData['airtime'],
                'name' => $name,
                'network' => $showMetaData['network']->name,
                'details' => array(
                    'summary' => $showMetaData['summary'],
                    'type' => $showMetaData['type'],
                    'status' => $showMetaData['status'],
                    'rating' => $showMetaData['rating']->average
                )
            );
        }   
    }

    // echo "<pre>";
    // print_r($topTenShows);
    // echo "</pre>";

    echo $twig->render('tv-schedule.html', ['tvshows_to_display' => $tvshows_to_display]);

