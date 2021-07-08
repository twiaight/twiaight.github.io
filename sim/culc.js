var count;

/**
 * ニュートン法による根の計算
 */
function Newton() {
  console.log("x^3 + x - 1 のニュートン法による数値計算");

  count = 0;
  let a = 1.0;
  let b;
  console.log("初期値 a=" + a);

  while (1) {
    b = a - func_y(a) / func_z(a); // 式(1.9)
    count++;
    console.log(b);
    if (Math.abs(a - b) < EPS) break;  // 収束判定
    else a = b;

    if ( count > 100 ) break;
  }
  console.log("近似解 x = " + b);
  console.log("計算回数:" + count);
}

/**
 * 根を求めたい関数
 * @param {number} x X座標
 * @return {number} Y座標
 */
function func_y(x) {
  return Math.pow(x, 3.0) + x - 1.0;
}

/**
 * 根を求めたい関数の微分関数
 * @param {number} x X座標
 * @return {number} Y座標
 */
function func_z(x) {
  return 3.0 * Math.pow(x, 2.0) + 1.0;
}

//////////////////////////

/**
 * 2分法による根の計算
 */
function Bisection() {
  let a = 0.0,
    b = 1.0; // 初期値

  count = 0;
  console.log("x^3 + x - 1 の2分法による数値計算");
  console.log("初期値 a=" + a);
  console.log("初期値 b=" + b);
  let x = nibun(a, b); // 解
  console.log("近似解 x = " + x);
  console.log("計算回数:" + count);
}

/**
 * 実際の計算部分
 * @param {number} a 計算範囲
 * @param {number} b 計算範囲
 * @return {number} 近似解
 */
function nibun(a, b) {
  let c;

  do {
    c = (a + b) / 2.0; // 2分計算
    console.log(c);
    if (func_y(c) * func_y(a) < 0) b = c; // 式(1.2)
    else a = c; // 式(1.3)

    count++;
  } while (Math.abs(a - b) > EPS); // 収束判別　式(1.4)の変形
  return c;
}

/**
 * 根を求めたい関数
 * @param {number} x X座標
 * @return {number} Y座標
 */
function func_y(x) {
  return Math.pow(x, 3.0) + x - 1.0;
}

var EPS = 0.0001; // 許容誤差
console.log('\n' + "0.0001");
Newton();
Bisection();


EPS = 0.0000001;
console.log('\n' + "0.0000001");
Newton();
Bisection();


EPS = 0.000000001;
console.log('\n' + "0.000000001");
Newton();
Bisection();
