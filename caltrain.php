<html>
<head>

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
	<script type="text/javascript">
	
		var tweets;
		var times = JSON.parse('<?php
			$url = "http://thenexttrain.com/at/caltrain/mountain-view/";
			$options = array(
					CURLOPT_HTTPHEADER     => array(
						"Accept: application/json",
						"User-Agent: AbelTrain http://abeltrain.com"
						),
					CURLOPT_RETURNTRANSFER => 1
					);
			$ch      = curl_init($url);
			curl_setopt_array($ch,$options);
			$content = curl_exec($ch);
			$err     = curl_errno($ch);
			$errmsg  = curl_error($ch) ;
			$header  = curl_getinfo($ch);
			curl_close($ch);

			echo $content;

		?>');
		var north_times = [];
		var south_times = [];

		for (var i=0; i<times.departures.length; i++) {
			var d = times.departures[i];
			if (d.headsign.indexOf("San Francisco") != -1) {
				north_times.push(d);
			} else { 
				south_times.push(d);
			}
		}

		$(function() {

			tweets_url = "http://api.twitter.com/1/statuses/user_timeline.json?screen_name=caltrain"
			$.ajax({
				url: tweets_url,
				data: { 
					screen_name: 'caltrain',
					count: 5
				},
				dataType: "jsonp",
				jsonp: "callback",
				jsonpCallback: "onTweetsReceived"
			})

			//departures_url = "mv_times.php"

			/*
			$.ajax({
				url: departures_url,
				dataType: "json",
				success: onTimesReceived,
				//headers: {
				//	"User-Agent": "AbelTrain http://abeltrain.com/",
				//	"Accept": "application/json"
				//},
				//jsonp: "callback",
				//jsonpCallback: "onTimesReceived"
			});
			*/
			display_departure_times(north_times, "#nbound_times")	
			display_departure_times(south_times, "#sbound_times");
		});

		function display_departure_times(times, div_id) {
			var html = '<table class="departure_times">';
			for (var i=0; i<times.length; i++) {
				train = times[i];
				html += '<tr><td class="headsign">' + train.headsign + '</td></tr><tr><td class="time">' + train.time + '</td></tr>';
			}
			html += '</table>';
			$(div_id).append(html);
		}

		function format_tweet_time(tweet_time) {
			var now = Date().toString().split(" ")[4];
			var now_tokens = now.split(":");

			var now_h = parseInt(parseFloat(now_tokens[0]));
			var now_m = parseInt(parseFloat(now_tokens[1]));

			var tweet_now = tweet_time.split(" ")[3];
			var tweet_tokens = tweet_now.split(":");
			var t_h = parseInt(parseFloat(tweet_tokens[0])) - 8;
			var t_m = parseInt(parseFloat(tweet_tokens[1]));
		

			if (now_h == t_h) { return "" + (now_m - t_m) + " minutes ago"; }
			else { return "" + (now_h-t_h)  + " hours ago"; }
		}

		function display_tweet(tweet) {
			var elem = $('<div class="tweet"></div>');
			elem.append('<div class="tweet_text">' + tweet.text + '</div>');
			elem.append('<div class="tweet_time">' + format_tweet_time(tweet.created_at) + '</div>');

			$("#caltrain_tweets").append(elem);

		}

		function onTweetsReceived(tweet_data) {
			tweets = tweet_data;

			for (var i=0; i<tweet_data.length; i++) {
				display_tweet(tweet_data[i]);
			}

		}

		function onTimesReceived(times_data) {
			times = times_data;
		}
	</script>

	<style>

		body {
			background-color: #cc0000;
			color: white;
			padding: 0 0;
			margin: 0 0;
			/* font-family: "Arial Rounded MT Bold", arial, serif; */
			font-family: Helvetica, arial, serif;
		}

		#container {
			width: 800px;
			margin: auto;
			padding: 40px 0 0 0;
		}

		#header {
			width: 100%;
			height: 150px;
			background-color: white;
			margin: 0 0;
			top: 0;
			left: 0;

			-webkit-box-shadow: 0 3px 8px #666;
		}

		#frames {
			height: 100%;
			padding-left: 20px;
		}

		h1 { font-size: 84px; }
		h2 { 
			margin: 20px 0 0 20px;
			padding: 0;
			font-size: 60px; 
			color: #4a71b0;
		}
		h3 { 
			width: 100%;
			margin: 20px 0 5px 5px;
			font-size: 48px; 
			color: black;
		}
		#caltrain_tweets {
			background-color: #d8ebfa;
			-webkit-box-shadow: -6px 8px 6px rgba(0, 0, 0, 0.25);
			margin-top: -107px;
			padding-top: 10px;
		}

		.right-col {
		}

		div.departure_times {
			height: 40%;
		}

		table.departure_times {
			width: 100%;
		}

		table.departure_times td {
			padding: 5px 2px 5px 2px;
			width: 100%;
		}

		table.departure_times td.time { 
			/*width: 75px;*/
			font-size: 36px;
		}

		table.departure_times td.headsign {
			font-size: 30px;
			margin-left: 10px;
			color: #DDD;
		}

		td { vertical-align: top; }

		td.left-col {
			width: 450px;
			height: 100%;
		}

		

		div.tweet {
			/* font-size: 36px; */
			/* margin: 20px 0; */
			padding: 30px 20px;
			border-bottom: 1px dashed #ccc;
		}

		div.tweet div.tweet_text {
			font-size: 48px;
			color:#333;
		}

		div.tweet div.tweet_time {
			font-size: 30px;
			color: #666;
			font-style: oblique;
		}

	</style>

</head>
<body>

<div id="header">
	<img src="caltrain_logo.jpg" height="125" style="display:block; padding-top:14px; padding-left: 25px" />
</div>
<table id="frames">
	<tr>
		<td class="left-col">
			<div id="nbound_times" class="departure_times">
				<h3>Northbound Trains</h3>
			</div>
			<div id="sbound_times" class="departure_times">
				<h3>Southbound Trains</h3>
			</div>
		</td>
		<td class="right-col">
			<div id="caltrain_tweets">
			<h2>@caltrain on Twitter</h2>
			</div>
		</td>
	</tr>
</table>
<!--
<div id="container">
	<div id="header"><h1>Caltrain Updates</h1></div>
	<div id="tweets"></div>

</div>
-->
</body>
</html>
