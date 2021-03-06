<?php
    $file = '../info.xml';
    
    $xml = @simplexml_load_file($file);

    $settings = @simplexml_load_file('../settings/settings.xml');
    if ($settings['first'] == 'true') {
        
?>

<script>window.location.replace('../settings?profile=1&first')</script>

<?php
        
    }
    /*foreach($settings->profile as $findProfile) {
        if ($findProfile['active'] == '1') {
            $profile = $findProfile;
        };
    };*/
    $profile = $settings->profile;
    
    if (!$xml) {
?>
<title>Error</title>
<h1>There are no songs saved.</h1>
<?php
    }else {
            
        function get_web_page($url) {
            $options = array(
                CURLOPT_RETURNTRANSFER => true,   // return web page
                CURLOPT_HEADER         => false,  // don't return headers
                CURLOPT_FOLLOWLOCATION => true,   // follow redirects
                CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
                CURLOPT_ENCODING       => "",     // handle compressed
                CURLOPT_USERAGENT      => "test", // name of client
                CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
                CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
                CURLOPT_TIMEOUT        => 120,    // time-out on response
            ); 
            
            $ch = curl_init($url);
            curl_setopt_array($ch, $options);
            
            $content  = curl_exec($ch);
            
            curl_close($ch);
            
            return $content;
        };
        
        $numsong = count($xml->song);
        $randnum = rand(1, $numsong);
        $eachtim = 0;
        
        $bodytext = '';
        
        foreach($xml->song as $song) {
            ++$eachtim;
            
            if (!file_exists('img/')) {
                mkdir('img/');
            };
            $dir = 'img/'.preg_replace('/[\<\>\:\"\/\\\\\|\?\*]/', '', $song->artist).' - '.preg_replace('/[\<\>\:\"\/\\\\\|\?\*]/', '', $song->album).'/';
            if (!file_exists($dir) || !file_exists($dir.'60x60.jpg') || !file_exists($dir.'1200x1200.jpg')) {
                
                $response = get_web_page('http://itunes.apple.com/search?country=US&media=music&term='.urlencode($song->artist).'+'.urlencode($song->album));
                $resArr = array();
                $resArr = json_decode($response);
                if (!isset($resArr->results[0]->artworkUrl100) || empty($resArr->results[0]->artworkUrl60) || !$resArr->results[0]->artworkUrl60) {
                    $urlartistreplace = urlencode(preg_replace("/(\s[a-zA-Z]+\.)?\s([a-zA-Z0-9_&#\-\\\\\/%\(\)]+)$/", '', preg_replace('/\+/', ' ', urlencode($song->artist))));
                    $urlalbumreplace = urlencode(preg_replace("/(\s[a-zA-Z]+\.)?\s([a-zA-Z0-9_&#\-\\\\\/%\(\)]+)$/", '', preg_replace('/\+/', ' ',  urlencode($song->album))));
                    $response = get_web_page('http://itunes.apple.com/search?country=US&media=music&term='.$urlartistreplace.'+'.$urlalbumreplace);
                    $resArr = array();
                    $resArr = json_decode($response);
                };
                
                if (!file_exists($dir)) {
                    mkdir($dir);
                }
                if (!file_exists($dir.'60x60.jpg')) {
                    copy($resArr->results[0]->artworkUrl60, $dir.'60x60.jpg');
                }
                if (!file_exists($dir.'1200x1200.jpg')) {
                    copy(str_replace('100x100','1200x1200',$resArr->results[0]->artworkUrl100), $dir.'1200x1200.jpg');
                }
            };
            
            $url = 'img/'.str_replace('+', '%20', urlencode(preg_replace('/[\<\>\:\"\/\\\\\|\?\*]/', '', $song->artist))).'%20-%20'.str_replace('+', '%20', urlencode(preg_replace('/[\<\>\:\"\/\\\\\|\?\*]/', '', $song->album)));
            $art = $url.'/1200x1200.jpg';
            $artsmall = $url.'/60x60.jpg';
            
            if ($eachtim == $randnum) {
                echo '<head><title id="title">Saved Songs</title><link rel="shortcut icon" type="image/jpeg" href="'.$artsmall.'"><link rel="stylesheet" href="themes/'.$profile->themeName.'/main.css"></head>';
            };
            
            $oldvar = $GLOBALS['bodytext'];
            $currentvar = '<div class="song"><div class="img"><a name="'.urlencode($song->artist).'+-+'.urlencode($song->songname).'" href="#'.urlencode($song->artist).'+-+'.urlencode($song->songname).'"><img class="artwork" src="'.$art.'"></a><span class="number">'.$eachtim.'</span></div><div class="allinfo"><div class="songinfo"><div class="songname"><a target="_blank" href="https://www.google.com/search?q='.urlencode($song->artist).'+-+'.urlencode($song->songname).'">'.$song->songname.'</a></div><div class="album"><a target="_blank" href="https://www.google.com/search?q='.urlencode($song->artist).'+-+'.urlencode($song->album).'">'.$song->album.'</a></div><div class="artist"><a target="_blank" href="https://www.google.com/search?q='.urlencode($song->artist).'">'.$song->artist.'</a></div></div><div class="info"><div class="timeAdded"><div class="dayOfWeek">'.$song->info->timeAdded->dayOfWeek.'</div><div class="date"><div class="month">'.$song->info->timeAdded->date->month.'</div><div class="day">'.$song->info->timeAdded->date->day.'</div><div class="year">'.$song->info->timeAdded->date->year.'</div></div><div class="time"><div class="hour">'.$song->info->timeAdded->time->hour.'</div><div class="min">'.$song->info->timeAdded->time->min.'</div><div class="sec">'.$song->info->timeAdded->time->sec.'</div><div class="period">'.$song->info->timeAdded->time->period.'</div></div></div></div></div></div>';
            
            $GLOBALS['bodytext'] = $oldvar.$currentvar;
            
        };
        
        echo '<body><div id="alert"></div>'.$bodytext.'</body>';
        
?>

<script>
    
    function myKeyPress(e){
        
        var keynum;
        
        if(window.event){ // IE					
        	keynum = e.keyCode;
        }else if(e.which){ // Netscape/Firefox/Opera					
        	keynum = e.which;
        }
        alert(String.fromCharCode(keynum));
    }
    
    document.onkeypress = function(e) {
        console.log(e.which);
        
    };
    
    /*var getAverageColor = (function(window, document, undefined){
	return function(imageURL, options}){
		options = {
			// image split into blocks of x pixels wide, 1 high
			blocksize: options.blocksize || 5,
			fallbackColor: options.fallbackColor || '#000'
		};

		var img = document.createElement('img'),
			canvas = document.createElement('canvas'),
			context = canvas.getContext && canvas.getContext('2d'),
			i = -4,
			count = 0,
			rgb = {
				r: 0,
				g: 0,
				b: 0
			},
			data, width, height, length;

		if(!context){
			return options.fallback_color;
		}

		height = canvas.height = img.naturalHeight || img.offsetHeight || img.height;
		width = canvas.width = img.naturalWidth || img.offsetWidth || img.width;

		// create a dom element for the image
		img.src = imageURL;
		img.id = 'averageColorImg';
		img.style.display = 'none';

		// draw image in canvas to make calculation easier
		context.drawImage(img, 0, 0);

		try {
			data = context.getImageData(0, 0, width, height);
		}
		catch(e){
			// security error, img on different domain
			return options.fallback_color;
		}

		length = data.data.length;

		// get rgb values for each pixel at the end of the block
		while((i += blocksize * 4) < length){
			++count;
			rgb.r += data.data[i];
			rgb.g += data.data[i+1];
			rgb.b += data.data[i+2];
		}

		// ~~used to floor values
		rgb.r = ~~(rgb.r/count);
		rgb.g = ~~(rgb.g/count);
		rgb.b = ~~(rgb.b/count);

		return 'rgb('+rgb.r+', '+rgb.g+', '+rgb.b+')';
	};
})(this, this.document);*/
    
    (function() {
        
        var hash;
        hash = window.location.hash;
        console.log(hash);
        window.location.replace('#');
        if (hash.length > 1) setTimeout(function() {window.location.replace(hash)}, 10);
        
        var currentXML = '';
        
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                currentXML = xhttp.responseText;
            
                updatePage(currentXML);
            };
        };
        xhttp.open("GET", "../info.xml", true);
        xhttp.send();
        
        checkVersion();
        
        for(var i=0;document.getElementsByTagName('img').length>i;i++) {
            //document.getElementsByClassName('number')[i].setAttribute('style', 'color:'+getAverageColor(document.getElementsByClassName('artwork')[i]))
        }
        
    })();
    
    function updatePage(currentXML) {
        
        var newXML = '';
        
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                newXML = xhttp.responseText;
                
                if (currentXML === newXML) {
                
                    setTimeout(function() {updatePage(currentXML)}, 1000);
                    
                }else {
                    
                    window.location.reload();
                    
                };
            };
        };
        xhttp.open("GET", "../info.xml", true);
        xhttp.send();
        
    };
    
    function postRequest (url, params, success, error) {  
        var xhr = XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP"); 
        xhr.open("GET", url, true); 
        xhr.onreadystatechange = function(){ 
            if ( xhr.readyState == 4 ) { 
                if ( xhr.status == 200 ) { 
                    success(xhr.responseText); 
                } else { 
                    error(xhr, xhr.status); 
                } 
            } 
        }; 
        xhr.onerror = function () { 
            error(xhr, xhr.status); 
        }; 
        xhr.send(params); 
    };
    
    function randomString(length, chars) {
        var result = '';
        for (var i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
        return result;
    }
    
    var title = document.getElementById('title');
    var titleText;
    titleText = title.innerHTML;
    
    function checkVersion() {
        var ranStr = randomString(32, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
        var version;
        var currentVersion;
        
        postRequest ('https://raw.githubusercontent.com/jaketr00/saveMusicInfo/master/version.txt?v='+ranStr, null, function (response1) {
            
            postRequest ('../version.txt?v='+ranStr, null, function (response2) {
                
                while (document.getElementById('alert').hasChildNodes()) {
                    document.getElementById('alert').removeChild(document.getElementById('alert').lastChild);
                }
                
                version = response1;
                currentVersion = response2;
                if (parseInt(currentVersion.replace(/\./g, '')) === parseInt(version.replace(/\./g, ''))) {
                    title.innerHTML = titleText;
                }else {
                    var alert = document.createElement("div");
                    alert.setAttribute('id', 'alertText');
                    if (parseInt(currentVersion.replace(/\./g, '')) < parseInt(version.replace(/\./g, ''))) {
                        alert.innerHTML = 'New version available';
                        title.innerHTML = titleText+' (Update)';
                    }else if (parseInt(currentVersion.replace(/\./g, '')) > parseInt(version.replace(/\./g, ''))) {
                        alert.innerHTML = 'Older version available ???';
                        title.innerHTML = titleText+' (Downdate?)';
                    }
                    document.getElementById('alert').appendChild(alert);
                }
                
            }, function (xhr, status) {
                
                while (document.getElementById('alert').hasChildNodes()) {
                    document.getElementById('alert').removeChild(document.getElementById('alert').lastChild);
                }
                
                var alert = document.createElement("div");
                title.innerHTML = titleText+' (Error)';
                alert.setAttribute('id', 'alertText');
                switch(status) { 
                    case 404:
                        alert.innerHTML = 'Error reading local file (File Not Found)';
                        break;
                    case 500:
                        alert.innerHTML = 'Error reading local file (Server Error)';
                        break;
                    case 0:
                        alert.innerHTML = 'Error reading local file (Request Aborted)';
                        break;
                    default:
                        alert.innerHTML = 'Error reading local file (Unknown Error: '+status+')';
                }
                document.getElementById('alert').appendChild(alert);
            });
            
        }, function (xhr, status) {
            
            while (document.getElementById('alert').hasChildNodes()) {
                document.getElementById('alert').removeChild(document.getElementById('alert').lastChild);
            }
            
            var alert = document.createElement("div");
            title.innerHTML = titleText+' (Error)';
            alert.setAttribute('id', 'alertText');
            switch(status) { 
                case 404:
                    alert.innerHTML = 'Error reading local file (File Not Found)';
                    break;
                case 500:
                    alert.innerHTML = 'Error reading local file (Server Error)';
                    break;
                case 0:
                    alert.innerHTML = 'Error reading local file (Request Aborted)';
                    break;
                default:
                    alert.innerHTML = 'Error reading local file (Unknown Error: '+status+')';
            }
            document.getElementById('alert').appendChild(alert);
        }); 
        
        
        setTimeout(function() {checkVersion()}, 60000);
    }
    
    window.onbeforeunload = function() {
        while (document.getElementById('alert').hasChildNodes()) {
            document.getElementById('alert').removeChild(document.getElementById('alert').lastChild);
        };
    };
    
</script>

<?php
        
    };
?>