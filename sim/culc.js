window.addEventListener("load", () => {
  var ui = new UI(
    document.querySelector("#start"),
    document.querySelector("#graph1")
  );
});

class UI {

  constructor(start, canvas) {
    this.start = start;
    
    this.canvas = canvas;
    this.ctx = canvas.getContext("2d");

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

  /**
   * ニュートン法による根の計算
   */
  Newton() {
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

  /**
   * 根を求めたい関数
   * @param {number} x X座標
   * @return {number} Y座標
   */
  func_y(x) {
    return Math.pow(x, 3.0) + x -  1.0;
  }

  /**
   * 根を求めたい関数の微分関数
   * @param {number} x X座標
   * @return {number} Y座標
   */
  func_z(x) {
    return 3.0 * Math.pow(x, 2.0) + 1.0;
  }

  //////////////////////////

  /**
   * 2分法による根の計算
   */
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

  /**
   * 実際の計算部分
   * @param {number} a 計算範囲
   * @param {number} b 計算範囲
   * @return {number} 近似解
   */
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

  /**
   * 根を求めたい関数
   * @param {number} x X座標
   * @return {number} Y座標
   */
  func_y(x) {
    return Math.pow(x, 3.0) + x - 1.0;
  }
  
  axis() {
    let ctx2 = this.canvas.getContext('2d');
    ctx2.beginPath();
    ctx2.moveTo(0,this.canvas.height/2);
    ctx2.lineTo(this.canvas.width,this.canvas.height/2);
    ctx2.moveTo(this.canvas.width/2,0);
    ctx2.lineTo(this.canvas.width/2,this.canvas.height);
    ctx2.stroke();
  }
}
