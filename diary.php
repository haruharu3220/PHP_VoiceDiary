<?php
//データまとめ用の空文字変数
$_POST['diary'];

// データ1件を1行にまとめる（最後に改行を入れる）
$write_data = "日記「{$_POST['diary']}」。 \n";

//ファイルを開く
// $file = fopen('data/questions.txt', 'a');
$pass = "data/" . date("Y-m-d_H時i分");
$file = fopen($pass, 'a');
//ファイルをロック
flock($file, LOCK_EX);

// 指定したファイルに指定したデータを書き込む
fwrite($file, $write_data);


//ロックを解除する
flock($file, LOCK_UN);
//ファイルを閉じる
fclose($file);
?> 


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>声日記</title>
</head>

<body>
    <h1>声日記</h1>
    

    <form action='diary.php' method="POST">
        <!-- <p type="text" name="diary" id="result-div"></p> -->
        <input name="diary" id="result-div"></input>
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
                    finalTranscript += transcript;
                } else {
                    interimTranscript = transcript;
                }
            }
            resultDiv.innerHTML = finalTranscript + '<i style="color:#ddd;">' + interimTranscript + '</i>';
        }

        speech.addEventListener('result', function (e) {
            var text = e.results[0][0].transcript;
            // 「ビデオ」と認識されたら指定の関数を実行
            console.log("text=" + text);
            setMesseage(text);
        });

        //https://www.codegrid.net/articles/2016-web-speech-api-1/
        //SpeechAPIが止まったら
        speech.onend = () => {
            if (!postData[postData.length - 1] === "ストップ") speech.stop()
        };


        function setMesseage(text) {

            if (text === ("送信" || "そうしん" || "ソウシン")) {
                $('button').click();
                
            }
            return text;
        }
    </script>

</body>

</html>
