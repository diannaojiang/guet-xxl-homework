<?php
error_reporting(E_ERROR);
$origin_text = $_POST['text']; //获得原文献

function match_eng($string, $encoding = 'utf8') {
	//预处理函数
	$string = preg_replace("/[^a-z\s]/i", "", $string); //删除掉英文字符以外
	$string = preg_replace("/\s\s+/", " ", $string); //删除重复空格
	$string = strtolower($string); //转换英文字符为小写
	return $string;
}

function pro_rand($pro_array) {
//根据概率生成随机字符
	$i = 1;
	foreach ($pro_array as $key => $value) {
		if (rand(0, 10000) / 10000 < $value / $i) {
			return $key;
		}
		$i -= $value;
	}
}

$match_text = match_eng($origin_text);
$zimu = 'abcdefghijklmnopqrstuvwxyz ';
$X = array(); //出现次数
$I = array(); //字符顺位
$P = array(); //出现概率
$x = 0; //总字符数
$H1 = 0; //H1信源熵
foreach (str_split($zimu) as $key => $value) {
	$X[$value] = substr_count($match_text, $value); //统计各字符出现次数
	$I[] = $value;
	$x += $X[$value];
}
foreach ($X as $key => $value) {
	$P[$key] = $value / $x; //统计各字符出现概率
	$H1 += $P[$key] * log($P[$key], 2); //计算信源熵
}
$H1 = -$H1;
$H1_text = '';
for ($i = 0; $i < 300; $i++) {
	$H1_text = $H1_text . pro_rand($P); //以H1为信源生成300个字符的序列
}

$X2 = array(); //一阶出现次数
$P2 = array(); //一阶出现概率
$H2 = 0; //H1信源熵
for ($i = 1; $i < strlen($match_text); $i++) {
	$X2[$match_text[$i - 1]][$match_text[$i]]++;
}
foreach ($X2 as $char1 => $char2) {
	$char1_count = 0;
	foreach ($char2 as $key => $char2_count) {
		$char1_count += $char2_count; //统计前字母出现的次数
		//因为不能为末尾字符所以不直接使用之前的数据
	}
	foreach ($char2 as $key => $char2_count) {
		$P2[$char1][$key] = $char2_count / $char1_count;
		$H2 += $P[$char1] * $P2[$char1][$key] * log($P2[$char1][$key], 2); //计算信源熵
		//计算从特定前字母到后字母的概率
	}
}
$H2 = -$H2;
$H2_text = substr($H1_text, 0, 1); //使用H1生成首字母
for ($i = 1; $i < 300; $i++) {
	$H2_text = $H2_text . pro_rand($P2[$H2_text[$i - 1]]); //以H2为信源生成300个字符的序列
}

$X3 = array(); //二阶出现次数
$P3 = array(); //二阶出现概率
for ($i = 2; $i < strlen($match_text); $i++) {
	$X3[$match_text[$i - 2]][$match_text[$i - 1]][$match_text[$i]]++;
}
foreach ($X3 as $char1 => $char2) {
	foreach ($char2 as $char2_key => $char3) {
		$char2_count = 0;
		foreach ($char3 as $key => $char3_count) {
			$char2_count += $char3_count; //统计前字母出现的次数
			//因为不能为末尾字符所以不直接使用之前的数据
		}
		foreach ($char3 as $key => $char3_count) {
			$P3[$char1][$char2_key][$key] = $char3_count / $char2_count;
			//计算从特定前字母到后字母的概率
		}
	}
}
$H3_text = substr($H2_text, 0, 2); //使用H1H2生成首次字母
for ($i = 2; $i < 300; $i++) {
	$H3_text = $H3_text . pro_rand($P3[$H3_text[$i - 2]][$H3_text[$i - 1]]); //以H3为信源生成300个字符的序列
}

?>
<!--前端显示代码-->
<!DOCTYPE html>
<html>
<head>
	<title>信息论大作业</title>
	<link href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.0.0-beta3/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.0.0-beta3/js/bootstrap.min.js"></script>
	<style type="text/css">
		#lineTd {
		 background: #fff url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxsaW5lIHgxPSIwIiB5MT0iMCIgeDI9IjEwMCUiIHkyPSIxMDAlIiBzdHJva2U9ImJsYWNrIiBzdHJva2Utd2lkdGg9IjEiLz48L3N2Zz4=) no-repeat 100% center;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="row clearfix">
			<div class="col-md-12 column">
				<h1>信息论课程大作业</h1>
				<h5>学号：1800201821</h3>
				<div class="row clearfix">
					<div class="col-md-6 column">
						<h3>在下方填入文献</h5>
						<form action="./" method="post"  role="form">
							<div class="form-group">
								<textarea name="text" style="resize: both !important;"><?php echo $origin_text; ?></textarea>
							</div>
							<div class="form-group">
								<input type="submit" value="提交">
								<input type="reset" value="重置">
							</div>
						</form>
					</div>
					<div class="col-md-6 column">
						<h3>预处理后的文本</h5>
						<p style=" word-wrap: break-word;word-break: break-all;overflow: hidden;"><?php echo $match_text; ?></p>
					</div>
				</div>
			</div>
		</div>
		<div class="row clearfix">
			<div class="col-md-12 column">
				<div class="row clearfix">
					<div class="col-md-6 column">
						<h3>信源熵H₁输出</h5>
						<table class="table table-hover table-bordered">
							<thead>
								<th>符号</th>
								<th>概率</th>
								<th>符号</th>
								<th>概率</th>
								<th>符号</th>
								<th>概率</th>
								<th>符号</th>
								<th>概率</th>
							</thead>
							<tbody>
								<?php
for ($i = 0; $i < 7; $i++) {
	echo "<tr>";
	for ($j = 0; $j < 4; $j++) {
		echo "<td>" . $I[$j * 8 + $i] . "</td><td>" . round($P[$I[$j * 8 + $i]], 5) . "</td>";
	}

	echo "</tr>";
}
?>
							</tbody>
						</table>
						<p>H₁(X)=<?php echo round($H1, 5); ?></p>
					</div>
					<div class="col-md-6 column">
					</div>
				</div>
			</div>
		</div>
		<div class="row clearfix">
			<div class="col-md-12 column">
						<h3>信源熵H₂输出</h5>
						<table class="table table-hover table-bordered">
							<thead>
								<th id="lineTd">
									<span style="float:right;margin-top:-10px;">后</span>
									<span style="float:left;margin-top:20px;">前</span>
								</th>
								<?php
foreach ($I as $key => $value) {
	echo "<th>" . $value . "</th>";
}?>
							</thead>
							<tbody>
								<?php
for ($i = 0; $i < 27; $i++) {
	echo "<tr>";
	echo "<td>" . $I[$i] . "</td>";
	for ($j = 0; $j < 27; $j++) {
		echo "<td>" . round($P2[$I[$i]][$I[$j]], 3) . "</td>";
	}
	echo "</tr>";
}
?>
							</tbody>
						</table>
						<p>H₂(X)=<?php echo round($H2, 5); ?></p>
			</div>
		</div>
		<div class="row clearfix">
			<div class="col-md-12 column">
				<div class="row clearfix">
					<div class="col-md-4 column">
						<h3>H₁序列生成</h5>
						<p style=" word-wrap: break-word;word-break: break-all;overflow: hidden;"><?php echo $H1_text; ?></p>
					</div>
					<div class="col-md-4 column">
						<h3>H₂序列生成</h5>
						<p style=" word-wrap: break-word;word-break: break-all;overflow: hidden;"><?php echo $H2_text; ?></p>
					</div>
					<div class="col-md-4 column">
						<h3>H₃序列生成</h5>
						<p style=" word-wrap: break-word;word-break: break-all;overflow: hidden;"><?php echo $H3_text; ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>