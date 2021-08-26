<?php



//没用了


//http://localhost:8080/?url=https://v.douyin.com/w84nHk/

$matchVideoUrlReg = '/https:\/\/(aweme|dou)\.(.*?)\.(com)([\w\.\?\/\=\&\;]*)?/'; // 匹配中转页视频地址
$matchVideoTitleReg = '/<div class="user-title">(.*?)<\/div>/';// 中转页视频标题
$matchVideoThumbnailReg = '/<img src="(.*?)" alt="封面">/';// 中转页视频封面
$matchVideoAuthorReg = '/<p class="user-info-name">(.*?)<\/p>/';// 中转页视频作者名称
$matchVideoAuthorAvatorReg = '/<div class="user-avator" style="background-image\:url\((.*?)\)"><\/div>/';// 中转页视频作者
$matchVideoAuthorIdReg = '/<p class="user-info-id">(.*?)<\/p>/';// 中转页作者抖音号
$matchVideoAllPlatformUrlReg = '/http:\/\/(.*?)\.(.*?)\.(com)([\w\.\-\?\/\=\&\;\%]*)?/';// 全平台无水印视频地址

if (!empty($_GET['url'])) {
    $url = $_GET['url'];
    $str = GET($url, 1);
    preg_match($matchVideoUrlReg, $str, $matchVideoUrlResult);
    preg_match($matchVideoTitleReg, $str, $matchVideoTitleResult);
    preg_match($matchVideoThumbnailReg, $str, $matchVideoThumbnailResult);
    preg_match($matchVideoAuthorReg, $str, $matchVideoAuthorResult);
    preg_match($matchVideoAuthorAvatorReg, $str, $matchVideoAuthorAvatorResult);
    preg_match($matchVideoAuthorIdReg, $str, $matchVideoAuthorIdResult);
    if (count($matchVideoUrlResult) >= 1 && count($matchVideoTitleResult) >= 2 && count($matchVideoThumbnailResult) >= 2 && count($matchVideoAuthorResult) >= 2 && count($matchVideoAuthorAvatorResult) >= 2 && count($matchVideoAuthorIdResult) >= 2) {
        $videoUrl = str_replace('playwm', 'play', $matchVideoUrlResult[0]); //手机访问可以无水印
        $videoTitle = $matchVideoTitleResult[1]; // 标题
        $videoThumbnail = $matchVideoThumbnailResult[1]; // 缩略图
        $videoAuthor = $matchVideoAuthorResult[1]; // 作者
        $videoAuthorAvator = $matchVideoAuthorAvatorResult[1]; // 头像
        $videoAuthorId = trim(str_replace('抖音ID:', '', $matchVideoAuthorIdResult[1])); // ID
        $output = Get($videoUrl); // 获取全平台无水印视频地址
        if ($output && (strpos($output, 'ixigua') !== false)) {
            preg_match($matchVideoAllPlatformUrlReg, $output, $matchVideoAllPlatformUrlResult);
            if (count($matchVideoAllPlatformUrlResult) >= 1) {
                echo json_encode([
                    'code' => 200,
                    'msg' => 'success',
                    'data' => [
                        'video_title' => $videoTitle,
                        'video_author' => $videoAuthor,
                        'video_author_id' => $videoAuthorId,
                        'video_author_avator' => $videoAuthorAvator,
                        'video_thumbnail' => $videoThumbnail,
                        'video_all_platform_url' => $matchVideoAllPlatformUrlResult[0],
                        'video_mobile_url' => $videoUrl
                    ]
                ]);
            }
        }
    }
} else {
    echo "null";
}

function Get($url, $foll = 0)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //成功返回结果，不输出结果，失败返回false
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //忽略https
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //忽略https
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["user-agent: Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25"]); //UA
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $foll); //默认为$foll=0,大概意思就是对照模块网页访问的禁止301 302 跳转。
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

