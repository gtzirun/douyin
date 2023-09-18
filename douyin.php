<!--感谢 hostloc bsah 提供的思路-->
<!--感谢 hostloc bsah 提供的思路-->
<!--感谢 hostloc bsah 提供的思路-->
<?php
$finalUrl = "";
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = $_POST['inputField'];

    // 处理用户输入，尝试获取视频ID或链接
    $videoIdOrLink = processUserInput($input);
    if (is_numeric($videoIdOrLink)) {
        // 如果输入是纯数字，则认为它是videoId
        $videoId = $videoIdOrLink;
    } else if (preg_match('/v\.douyin\.com\/[a-zA-Z0-9]+/', $videoIdOrLink)) {
        // 从链接中提取视频ID
        $videoId = extractVideoId($videoIdOrLink);
    } else {
        $errorMsg = "输入无法识别";
    }

    if ($videoId) {
        $apiUrl = "https://www.iesdouyin.com/web/api/v2/aweme/iteminfo/?reflow_source=reflow_page&item_ids={$videoId}&a_bogus=64745b2b5bdc4e75b720a9a85b19867a";
        $response = file_get_contents($apiUrl);
        $data = json_decode($response, true);

        if (!empty($data['item_list'][0]['video']['play_addr']['uri'])) {
            $uri = $data['item_list'][0]['video']['play_addr']['uri'];
            $finalUrl = "www.iesdouyin.com/aweme/v1/play/?video_id={$uri}&ratio=1080p&line=0";
        }
    } else if (!$errorMsg) {
        $errorMsg = "无法获取视频ID";
    }
}

function processUserInput($input) {
    preg_match('/v\.douyin\.com\/[a-zA-Z0-9]+/', $input, $matches);
    if (!empty($matches)) return $matches[0];

    preg_match('/\d{19}/', $input, $matches);
    if (!empty($matches)) return $matches[0];

    return null;
}

function extractVideoId($link) {
    $redirectLink = getRedirectUrl($link);
    preg_match('/\/video\/(\d+)\//', $redirectLink, $idMatches);
    return !empty($idMatches) ? $idMatches[1] : null;
}

function getRedirectUrl($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    preg_match('/^Location: (.+)$/mi', $response, $matches);
    return !empty($matches[1]) ? trim($matches[1]) : null;
}
?>

<!-- ... PHP部分代码不变 ... -->

<!-- ... PHP部分代码不变 ... -->

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>抖音链接处理</title>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@4.5.2/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    输入抖音链接
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="inputField">输入信息:</label>
                            <input type="text" class="form-control" id="inputField" name="inputField" placeholder="输入包含抖音链接的文本">
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">提交</button>
                        </div>
                        <?php if($finalUrl): ?>
                        <div class="alert alert-success" role="alert">
                            结果链接: <?php echo $finalUrl; ?>
                        </div>
                        <?php elseif($errorMsg): ?>
                        <div class="alert alert-danger" role="alert">
                            错误: <?php echo $errorMsg; ?>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://unpkg.com/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://unpkg.com/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
</body>
</html>

