const start = document.querySelector("#start");
const clear = document.querySelector("#clear");
const next = document.querySelector("#next");
const button1 = document.querySelector("#b01");
const button2 = document.querySelector("#b02");
const button3 = document.querySelector("#b03");
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

ctx_c1.strokeStyle = "rgb(0, 0, 255)";
ctx_c2.strokeStyle = "rgb(0, 0, 255)";

var sequence = 0;
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

var stack = undefined;

function eps() {
  console.log(button.value);
}

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
    result2.push(b);
    let x = nibun(a, b); // 解
    // document.write("近似解 x = " + x + "<br>");
    // document.write("計算回数:" + count + "<br>");
}

function Newton() {
    console.log("x^3 + x - 1 のニュートン法による数値計算");

    count1 = 0;
    let a = 1.0;
    let b;
    console.log("初期値 a=" + a);
    result1.push(a);

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
  sub();
  
  Newton();
  Bisection();
  
  requestAnimationFrame(draw);
}

function nextsequence() {
  sequence = 1;
}

function draw() {
  
  if( sequence == 1 ) {
    
    if ( flag1 == 0 ) text1 = output1.pop()
    if ( text1 != undefined ) {
      comp1.innerHTML += text1 + "<br>";
      draw1(text1);
    } else flag1 = 1;
      
    if ( flag2 == 0 ) text2 = output2.pop()
    if ( text2 != undefined ) {
      comp2.innerHTML += text2 + "<br>";
      draw2(text2);
    } else flag2 = 2;  
      
    sequence = 0;
  }
  
  if ( flag1 == 1 && flag2 == 1 ) cancelAnimationFrame();
  else requestAnimationFrame(draw);
}

function axis() {
  ctx_b1.beginPath();
  ctx_b1.moveTo(0, b1.height/2);
  ctx_b1.lineTo(b1.width, b1.height/2);
  ctx_b1.moveTo(0, 0);
  ctx_b1.lineTo(0, b1.height);
  ctx_b1.stroke();
    
  ctx_b2.beginPath();
  ctx_b2.moveTo(0, b2.height/2);
  ctx_b2.lineTo(b2.width, b2.height/2);
  ctx_b2.moveTo(0, 0);
  ctx_b2.lineTo(0, b2.height);
  ctx_b2.stroke();
  
  func();
}

function func() {
  let now  = -1.0;
  let next = 0;
  let x, y;
  
  for ( let i=1; i<460; i++ ) {
    x = i/460;
    y = Math.pow(x, 3.0) + x -  1.0;
    next = y;
    
    ctx_b1.beginPath();
    ctx_b1.moveTo((i-1), b1.height/2+now*230);
    ctx_b1.lineTo(i, b1.height/2 +  next*230);
    ctx_b1.stroke();
    
    ctx_b2.beginPath();
    ctx_b2.moveTo((i-1), b2.height/2+now*230);
    ctx_b2.lineTo(i, b2.height/2 +  next*230);
    ctx_b2.stroke();
    
    now = next;
  }
}

function draw1(x) {
  if ( stack == undefined ) {
    stack = x;
  } else {
    let y = Math.pow(stack, 3.0) + stack -  1.0;
    
    ctx_c1.beginPath();
    ctx_c1.moveTo(stack*460,c1.height/2);
    ctx_c1.lineTo(stack*460,c1.height/2 + y*230);
    ctx_c1.lineTo(x*460, c1.height/2);
    ctx_c1.stroke();
    
    stack = x;
  }
}

function draw2(x) {
  ctx_c2.beginPath();
  ctx_c2.moveTo(x*460,0);
  ctx_c2.lineTo(x*460, c2.height);
  ctx_c2.stroke();
}

function sub() {
  flag1 = 0;
  flag2 = 0;
  comp1.innerHTML = "";
  comp2.innerHTML = "";
  result1 = [];
　result2 = [];
　output1 = [];
　output2 = [];
　stack = undefined;
　sequence = 0;
　ctx_c1.clearRect(0, 0, c1.width, c1.height);
　ctx_c2.clearRect(0, 0, c2.width, c2.height);
}

window.addEventListener("load", axis);
start.addEventListener("click", main);
clear.addEventListener("click", sub);
next.addEventListener("click",  nextsequence);
button1.addEventListener("click", () => { EPS = Number(button1.value) });
button2.addEventListener("click", () => { EPS = Number(button2.value) });
button3.addEventListener("click", () => { EPS = Number(button3.value) });
