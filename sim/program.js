const start = document.querySelector("#start");
const c1 = document.querySelector("#graph1");
const b1 = document.querySelector("#graph1b");
const c2 = document.querySelector("#graph2");
const b2 = document.querySelector("#graph2b");

const comp1 = document.querySelector("#comp1");
const comp2 = document.querySelector("#comp2");

const ctx_c1 = c1.getContext('2d');
const ctx_b1 = b1.getContext('2d');
const ctx_c2 = c2.getContext('2d');
const ctx_b2 = b2.getContext('2d');

var time = 0;
var count1 = 0;
var count2 = 0;

var EPS = 0.0001; // 許容誤差
var result1 = [];
var result2 = [];

var output1 = [];
var output2 = [];

var flag1 = 0;
var flag2 = 0;

var text1;
var text2;

function func_y(x) {
    return Math.pow(x, 3.0) + x -  1.0;
}

function func_z(x) {
    return 3.0 * Math.pow(x, 2.0) + 1.0;
}

function nibun(a, b) {
    let c;

    do {
      c = (a + b) / 2.0; // 2分計算
      // document.write(c + "<br>");
      result2.push(c);
      if (func_y(c) * func_y(a) < 0) b = c; // 式(1.2)
      else a = c; // 式(1.3)

      count2++;
    } while (Math.abs(a - b) > EPS); // 収束判別　式(1.4)の変形
    
    for (var now=result2.pop(); now!=undefined; now=result2.pop()) {
        output2.push(now);
    }
    
    return c;
}

function Bisection() {
    let a = 0.0, b = 1.0; // 初期値

    count2 = 0;
    console.log("x^3 + x - 1 の2分法による数値計算");
    console.log("初期値 a=" + a);
    console.log("初期値 b=" + b);
    let x = nibun(a, b); // 解
    // document.write("近似解 x = " + x + "<br>");
    // document.write("計算回数:" + count + "<br>");
}

function Newton() {
    console.log("x^3 + x - 1 のニュートン法による数値計算");

    count = 0;
    let a = 1.0;
    let b;
    console.log("初期値 a=" + a);

    while (1) {
      b = a - func_y(a) / func_z(a); // 式(1.9)
      count1++;
      // document.write(b + "<br>");
      result1.push(b);
      if (Math.abs(a - b) < EPS) break;  // 収束判定
      else a = b;

      if ( count1 > 100 ) break;
    }
    
    for (var now=result1.pop(); now!=undefined; now=result1.pop()) {
      output1.push(now);
    }
    
    // document.write("近似解 x = " + b + "<br>");
    // document.write("計算回数:" + count + "<br>");
}

function main() {
  Newton();
  Bisection();
  
  requestAnimationFrame(draw);
}

function draw() {
  
  
  
  time++;
  if( time >= 60 ) {
    if ( flag1 == 0 ) text1 = output1.pop()
    if ( text1 != undefined ) comp1.innerHTML += text1 + "<br>";
    else flag1 = 1;
      
    if ( flag2 == 0 ) text2 = output2.pop()
    if ( text2 != undefined ) comp2.innerHTML += text2 + "<br>";
    else flag2 = 2;  
      
    time = 0;
  }
    
  requestAnimationFrame(draw);
}

function axis() {
  ctx_b1.beginPath();
  ctx_b1.moveTo(0, b1.height/2);
  ctx_b1.lineTo(b1.width, b1.height/2);
  ctx_b1.moveTo(b1.width/2,0);
  ctx_b1.lineTo(b1.width/2, b1.height);
  ctx_b1.stroke();
    
  ctx_b2.beginPath();
  ctx_b2.moveTo(0, b2.height/2);
  ctx_b2.lineTo(b2.width, b2.height/2);
  ctx_b2.moveTo(b2.width/2,0);
  ctx_b2.lineTo(b2.width/2, b2.height);
  ctx_b2.stroke();
}

window.addEventListener("load", axis);
start.addEventListener("click", main);


/*
class UI {

  constructor(start, c1, b1) {
    this.start = start;
    
    this.c1 = c1;
    this.ctxc1 = this.c1.getContext("2d");
    this.b1 = b1;
    this.ctxb1 = this.b1.getContext("2d");

    this.EPS = 0; // 許容誤差
    this.count = 0;
    this.result1 = [];
    this.result2 = [];
    
    this.axis();  
    
    this.start.addEventListener("click", () => {
      this.EPS = 0.0001;
      console.log('\n' + "0.0001");
      this.Newton();
      this.Bisection();
      this.Graph();
    });
  }
*/
  /**
   * ニュートン法による根の計算
   */
/*
  function Newton() {
    console.log("x^3 + x - 1 のニュートン法による数値計算");

    this.count = 0;
    let a = 1.0;
    let b;
    console.log("初期値 a=" + a);

    while (1) {
      b = a - this.func_y(a) / this.func_z(a); // 式(1.9)
      this.count++;
      document.write(b + "<br>");
      this.result1.push(b);
      if (Math.abs(a - b) < this.EPS) break;  // 収束判定
      else a = b;

      if ( this.count > 100 ) break;
    }
    document.write("近似解 x = " + b + "<br>");
    document.write("計算回数:" + this.count + "<br>");
  }
*/
  /**
   * 根を求めたい関数
   * @param {number} x X座標
   * @return {number} Y座標
   */
/*
 func_y(x) {
    return Math.pow(x, 3.0) + x -  1.0;
  } 
*/
  /**
   * 根を求めたい関数の微分関数
   * @param {number} x X座標
   * @return {number} Y座標
   */
/*
  func_z(x) {
    return 3.0 * Math.pow(x, 2.0) + 1.0;
  }
*/
  //////////////////////////

  /**
   * 2分法による根の計算
   */
/*
  Bisection() {
    let a = 0.0, b = 1.0; // 初期値

    this.count = 0;
    console.log("x^3 + x - 1 の2分法による数値計算");
    console.log("初期値 a=" + a);
    console.log("初期値 b=" + b);
    let x = this.nibun(a, b); // 解
    document.write("近似解 x = " + x + "<br>");
    document.write("計算回数:" + count + "<br>");
  }
*/
  /**
   * 実際の計算部分
   * @param {number} a 計算範囲
   * @param {number} b 計算範囲
   * @return {number} 近似解
   */
/*
  nibun(a, b) {
    let c;

    do {
      c = (a + b) / 2.0; // 2分計算
      document.write(c + "<br>");
      this.result2.push(c);
      if (this.func_y(c) * this.func_y(a) < 0) b = c; // 式(1.2)
      else a = c; // 式(1.3)

      this.count++;
    } while (Math.abs(a - b) > this.EPS); // 収束判別　式(1.4)の変形
    return c;
  }
*/
  /**
   * 根を求めたい関数
   * @param {number} x X座標
   * @return {number} Y座標
   */
/*
  func_y(x) {
    return Math.pow(x, 3.0) + x - 1.0;
  }
  
  axis() {
    this.ctxb1.beginPath();
    this.ctxb1.moveTo(0,this.b1.height/2);
    this.ctxb1.lineTo(this.b1.width,this.b1.height/2);
    this.ctxb1.moveTo(this.b1.width/2,0);
    this.ctxb1.lineTo(this.b1.width/2,this.b1.height);
    this.ctxb1.stroke();
  }
}
*/
