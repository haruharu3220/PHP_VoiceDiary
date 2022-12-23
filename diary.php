<?php

if (isset($_POST['diary'])) {
    $_POST['diary'];
    //ファイルを開く
    $objDateTime = new DateTime();
    $word =str_replace("送信","",$_POST['diary']);
    $write_data = "{$objDateTime->format('H:i:s')} :{$word} \n";

    $pass = "data/" . $objDateTime->format('Y-m-d').".txt";
    $file = fopen($pass, 'a');
    //ファイルをロック
    flock($file, LOCK_EX);

    // 指定したファイルに指定したデータを書き込む
    fwrite($file, $write_data);


    //ロックを解除する
    flock($file, LOCK_UN);
    //ファイルを閉じる
    fclose($file);
}
?> 


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>こころログ</title>
    <link href="new_style.css" rel="stylesheet" type="text/css">
</head>

<body>
    <h1>こころログ</h1>
    
    <form action='diary.php' method="POST">
        <!-- <p type="text" name="diary" id="result-div"></p> -->
        <dev  id="result-div"></dev>
        <input name="diary" id="diaryInput"></input>
        <div>
            <button>submit</button>
        </div>
    </form>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        const resultDiv = document.querySelector('#result-div');
        const speech = new webkitSpeechRecognition();
        speech.lang = 'ja-JP';
        speech.interimResults = true;
        speech.continuous = true;
        // 音声認識をスタート
        speech.start();
        //ワード保存用配列
        let postData = [];

        let finalTranscript = ''; // 確定した(黒の)認識結果

        //音声自動文字起こし機能
        speech.onresult = function (e) {
            let interimTranscript = ''; // 暫定(灰色)の認識結果
            for (let i = event.resultIndex; i < event.results.length; i++) {
                let transcript = event.results[i][0].transcript;
                if (event.results[i].isFinal) {
                    finalTranscript += transcript+'\n';
                } else {
                    interimTranscript = transcript;
                }
            }
            setMesseage(finalTranscript);
            resultDiv.innerHTML = finalTranscript +'<i style="color:#ddd;">' + interimTranscript + '</i>';
        
        
        }

        //https://www.codegrid.net/articles/2016-web-speech-api-1/
        //SpeechAPIが止まったら
        speech.onend = () => {
            if (!postData[postData.length - 1] === "ストップ") speech.stop()
        };


        function setMesseage(text) {

            if (text.indexOf("送信")!=-1) {
                $("#diaryInput").val($('#result-div').text());
                $('button').click();   
            }

            if (text === ("確認" || "かくにん" || "カクニン")) {
                $('button').click();
            }
            
            return text;
        }
    </script>

</body>

</html>
