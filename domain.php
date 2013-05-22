<?php
/**
 * 过期域名查询
 * @copyright (c) 2012-11
 * @author	Qiufeng <fengdingbo@gmail.com>
 * @link	http://www.fengdingbo.com
 * @version	0.1
 */
/**
 * paimi.com/search参数列表
 * p=分页数
 * q=关键字
 * w=位置 {int{0:任意;1:左;2:右;3:左或右}}
 * c1=总长度start{int}
 * c2=总长度end{int}
 * g%5B%5D=域名结构{w:字母;n:数字;h:中划线}
 * py=拼音{int{1}}
 * pl=拼音{:全部拼音,1:单拼音,2:双拼音,3:三拼音;4:四拼音}
 * vy=y为元音{int{1}}
 * suggest=百度谷歌建议{1}
 * o=结果排序{int{1:字母,2:长度,3:后缀,4:日期,5:日期和长度,6:PR值}}
 * h=后缀{com:.com,net:.net,org:.org,cc:.cc,co:.co}
 * dt=删除类型{other:Delete,namejet:Pre-Release,snapnames:Pre-Auction}
 * d=删除日期{0:即将删除;1:已删除;Y-m-d}
 */
set_time_limit(0);
date_default_timezone_set('Asia/Shanghai'); //定义时区
echo date("Y-m-d H:i:s"),"\n";
domain();
function domain()
{
	static $p=0;
	$p++;	
	$dom = new DOMDocument();
	@$dom->loadHTMLFile("http://www.paimi.com/search?p={$p}&q=&w=0&c1=5&c2=6&g%5B%5D=w&py=&pl=&vy=1&suggest=&o=2&h%5B%5D=com&dt=other&d=1");
	$xml = simplexml_import_dom($dom);
	$item = $xml->body->div->div[1]->div[1]->div->div->table->tbody->tr;
	$end = count($item);
	$i=0;
	foreach($item as $v)
	{
		$i++;
		domain_sreach($v->td[0]->a,$v->td[5]->a);
		if($end==$i) domain();
	}
	if($p>10){exit('0');};
}
function domain_sreach($domain,$suggest=null)
{
	$domain_sreach = file_get_contents("http://panda.www.net.cn/cgi-bin/check.cgi?area_domain={$domain}");
	preg_match('/<original>(.*)<\/original>/',$domain_sreach,$preg);
	$domain_state = (int)$preg[1];
	switch($domain_state)
	{
		case 210:
			echo "{$domain}	{$suggest}	可以注册\n";
			break;
		case 211:
			//echo "{$domain}	已被注册\n";
			break;
		case 212:
			echo "参数错误\n";	
			break;
	}
}