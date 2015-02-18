/* @author YukiKawasaki
 *  FileName registGroup.js
 *  @create 2015/01/05
 *  Remark フロントエンドで使いそうなバリデーション関数
 */
function isRequire(input) {
	return input !== "";
};
function isEmail(input) {
	return input.match(/^[A-Za-z0-9\.]+[\w-]+@[\w\.-]+\.\w{2,}$/);
}
function isLength(input, length) {
	return input.length <= length;
}
function isAlphabet(input) {
	return input.match(/^[A-Za-z0-9]+$/);
}