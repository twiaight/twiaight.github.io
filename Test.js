function main() {
  const canvas = document.querySelector("#glCanvas");
  // GL コンテキストを初期化する
  const gl = canvas.getContext("webgl");

  // WebGL が使用可能で動作している場合にのみ続行します
  if (gl === null) {
    alert("WebGL を初期化できません。ブラウザーまたはマシンがサポートしていない可能性があります。");
    return;
  }

  // クリアカラーを黒に設定し、完全に不透明にします
  gl.clearColor(0.0, 0.0, 0.0, 1.0);
  // 指定されたクリアカラーでカラーバッファをクリアします
  gl.clear(gl.COLOR_BUFFER_BIT);
}

window.onload = main;
