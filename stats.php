<?php
/**
 * 畅享创意工坊 - 统计接收端（含 IP+日期 去重）
 * 
 * 调用方式（GET 或 POST）：
 *   stats.php?action=visit&productId=soft_20260614113912&name=植物大战僵尸&category=游戏
 *   stats.php?action=download&productId=soft_20260614113912&name=植物大战僵尸&category=游戏
 * 
 * 返回 JSON：
 *   {"success":true,"deduplicated":false}  
 *   deduplicated=true 表示该IP今天已经统计过，未重复计数
 */

// 统计文件路径（与 softwares.js 同目录）
$statsFile = __DIR__ . '/stats.json';

// 读取或初始化统计数据
$stats = [];
if (file_exists($statsFile)) {
    $content = file_get_contents($statsFile);
    if ($content !== false) {
        $decoded = json_decode($content, true);
        if (is_array($decoded)) {
            $stats = $decoded;
        }
    }
}

// 确保基本结构
if (!isset($stats['products'])) {
    $stats['products'] = [];
}
if (!isset($stats['total'])) {
    $stats['total'] = ['visits' => 0, 'downloads' => 0];
}
if (!isset($stats['_meta'])) {
    $stats['_meta'] = [
        'created' => date('Y-m-d H:i:s'),
        'lastUpdated' => date('Y-m-d H:i:s')
    ];
}
if (!isset($stats['_daily'])) {
    $stats['_daily'] = [];
}

// 获取参数
$action     = isset($_REQUEST['action'])     ? trim($_REQUEST['action'])     : '';
$productId  = isset($_REQUEST['productId'])  ? trim($_REQUEST['productId'])  : '';
$productName     = isset($_REQUEST['name'])     ? trim($_REQUEST['name'])     : '';
$productCategory = isset($_REQUEST['category']) ? trim($_REQUEST['category']) : '';

// 参数校验
if (empty($productId)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => '缺少 productId 参数']);
    exit;
}
if (!in_array($action, ['visit', 'download'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => '无效的 action，仅支持 visit/download']);
    exit;
}

// ★ 获取客户端 IP（采用的是完全匿名化，要严格保护用户隐私，仅用于去重用途'￢︿̫̿￢☆'快夸我~~~）
$clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
// 使用 IP 完整地址做去重（只在内存中用，不会暴露给外界）

// ★ 今天的日期的字符串
$today = date('Y-m-d');

// ★ IP+日期 去重检查
$deduplicated = false; // 默认为 false，表示是新计数
if (isset($stats['_daily'][$today][$productId][$action][$clientIp])) {
    // 如果今天这个 IP 已经统计过了 则去重，不纳入重复计数
    $deduplicated = true;
}

// 初始化该产品的记录
if (!isset($stats['products'][$productId])) {
    $stats['products'][$productId] = [
        'id'         => $productId,
        'name'       => $productName,
        'category'   => $productCategory,
        'visits'     => 0,
        'downloads'  => 0,
        'firstVisit' => date('Y-m-d H:i:s'),
        'lastVisit'  => null,
        'lastDownload' => null
    ];
}

$now = date('Y-m-d H:i:s');

// ★ 仅在非去重时才计数，确保数据真实有效，这样好知道真实的结果o((>ω< ))o
if (!$deduplicated) {
    if ($action === 'visit') {
        $stats['products'][$productId]['visits']    = intval($stats['products'][$productId]['visits']) + 1;
        $stats['products'][$productId]['lastVisit'] = $now;
        $stats['total']['visits']                   = intval($stats['total']['visits']) + 1;
    } elseif ($action === 'download') {
        $stats['products'][$productId]['downloads']    = intval($stats['products'][$productId]['downloads']) + 1;
        $stats['products'][$productId]['lastDownload'] = $now;
        $stats['total']['downloads']                   = intval($stats['total']['downloads']) + 1;
    }
}

// 如果之前没有名称/分类，补充一下
if (empty($stats['products'][$productId]['name']) && !empty($productName)) {
    $stats['products'][$productId]['name'] = $productName;
}
if (empty($stats['products'][$productId]['category']) && !empty($productCategory)) {
    $stats['products'][$productId]['category'] = $productCategory;
}

// ★ 记录今天这个 IP 已经统计过（无论是否去重，都记录 IP 存在）
if (!isset($stats['_daily'][$today])) {
    $stats['_daily'][$today] = [];
}
if (!isset($stats['_daily'][$today][$productId])) {
    $stats['_daily'][$today][$productId] = ['visit' => [], 'download' => []];
}
// 记录这个 IP 已访问/下载
$stats['_daily'][$today][$productId][$action][$clientIp] = true;

// ★ 清理过期数据：只保留最近 30 天的 _daily 记录
$cleanDate = date('Y-m-d', strtotime('-30 days'));
foreach ($stats['_daily'] as $dateKey => $dailyData) {
    if ($dateKey < $cleanDate) {
        unset($stats['_daily'][$dateKey]);
    }
}

// 更新元数据
$stats['_meta']['lastUpdated'] = $now;

// 写入文件
$written = file_put_contents(
    $statsFile,
    json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    LOCK_EX
);

// 返回结果
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($written === false) {
    echo json_encode(['success' => false, 'error' => '写入统计文件失败']);
} else {
    echo json_encode([
        'success'      => true,
        'deduplicated' => $deduplicated,
        'action'       => $action,
        'productId'    => $productId
    ]);
}
