<?php
/**
 * 一言添加模块(the module of adding hitokoto)
 *
 */

if (!defined('DIR'))
exit('error');
if (!is_user_login()) {
    header('Location:/?login');
}

get_header('添加句子');

//获取一言(get hitokoto)
$hitokoto_content = isset($_POST['hitokoto_content']) ? $_POST['hitokoto_content'] : null;
$hitokoto_cat = isset($_POST['hitokoto_cat']) ? $_POST['hitokoto_cat'] : null;
$source = isset($_POST['source']) ? ($_POST['source'] == '来源' ? null : $_POST['source']) : null;

$db = new DB;
//检查必要参数（Check the necessary parameters）
if (isset($_POST['add_hitokoto']) ? $_POST['add_hitokoto'] : null == 1 && !empty($hitokoto_content) && !empty($hitokoto_cat)) {
    $find = check_hitokoto_similarity($hitokoto_content);
    //找到就添加，否则输出相似度（Add it if found, otherwise output similarity）
    if (!$find) {
        $array_hitokoto = array(
            'content' => $hitokoto_content,
            'cat' => $hitokoto_cat,
            'source' => $source,
        );
        //添加一言(add hitokoto)
        $result = $db->add_hitokoto($array_hitokoto, $_SESSION['userinfo']['userid']);
        if ($result['LAST_INSERT_ID()']) {
            echo '插入成功！ID：'.$result['LAST_INSERT_ID()'];
        } else {
            echo 'failed';
        }
    } else {
        echo '发现相似度极高的句子!不予添加！！！<br />' . $find;
    }
}

//获取分类并转为数组(get category and transform to array)
$cat = $db->get_option_value('cat');
$array_cat = json_decode($cat, true);

?>
<p>这里是添加句子</p>
你的句子：
<form method="post" action="/?add">
    <input type="text" name="hitokoto_content" onblur="if(this.value=='')this.value='呐，知道么，樱花飘落的速度，是每秒五厘米哦~';" onfocus="if(this.value=='呐，知道么，樱花飘落的速度，是每秒五厘米哦~')this.value='';" value="呐，知道么，樱花飘落的速度，是每秒五厘米哦~">
    <select name="hitokoto_cat">
        <?php
foreach ($array_cat as $key => $value) {
    echo '<option value="' . $key . '">' . $value . '</option>';
}
?>
    </select>
    <input type="text" name="source" onblur="if(this.value=='')this.value='来源';" onfocus="if(this.value=='来源')this.value='';" value="来源">
    <input type="submit" value="添加">
    <input type="hidden" name="add_hitokoto" value="1">
</form>
<br />
<?php
get_footer();