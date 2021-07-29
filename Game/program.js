
import * as THREE from 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.module.js';

import { PointerLockControls } from './Lib/PointerLockControls.js';

let scene, camera, renderer;
const canvas = document.querySelector('#mC');

var mouse = new THREE.Vector2(0, 0);

/* 基礎設定用 */
var width = 800, height = 600;
var geometry, material;

var prevTime = 0;
var controls;
const ray = new THREE.Raycaster();
var velocity = new THREE.Vector3();

/* 的 */
var sphere;
const sphereList = [];

var clock = new THREE.Clock();

init();
update();


function init() {

  /* 参考文献
  https://irukanobox.blogspot.com/2016/04/threejs_3.html
  https://irukanobox.blogspot.com/2016/11/threejs3d.html
  https://qiita.com/mczkzk/items/b8a59bfb272a05cb7109
  https://qiita.com/te26/items/5204fa0d220de2df6295
  https://mdn.github.io/dom-examples/pointer-lock/
  https://ics.media/tutorial-three/raycast/

  https://sbcode.net/threejs/pointerlock-controls/
  https://threejs.org/examples/misc_controls_pointerlock.html
  */

  renderer = new  THREE.WebGLRenderer({ canvas:canvas, antialias: true });
  renderer.setSize(width, height);
  document.body.appendChild(renderer.domElement);

  renderer.shadowMap.enabled = true;

  scene = new THREE.Scene();

  camera = new THREE.PerspectiveCamera(45, width/height, 1, 1000);
  camera.position.z = 2.5;
  camera.position.y = 0.5;

  mouse.x = 0;
  mouse.y = 0;

  controls = new PointerLockControls(camera, document.body );
  const instructions = document.getElementById( 'instructions' );
  const blocker = document.getElementById( 'blocker' );

  instructions.addEventListener( 'click', function () { controls.lock(); } );
  controls.addEventListener( 'lock', function () {
      instructions.style.display = 'none';
      blocker.style.display = 'none';
    } );
  controls.addEventListener( 'unlock', function () {
      blocker.style.display = 'block';
      instructions.style.display = '';
    } );

  scene.add( controls.getObject() );
  

  /* Axis -> x:RED / y:GREEN / z:BLUE */
  var axes = new THREE.AxesHelper(20);
  scene.add(axes);


  geometry = new THREE.SphereGeometry(1, 20, 20);
  
  material = new THREE.MeshPhongMaterial({ color:0xffffff });
  sphere = new THREE.Mesh(geometry, material);
  sphere.position.x = -2;
  scene.add(sphere);
  sphereList.push(sphere);

  material = new THREE.MeshPhongMaterial({ color:0xffffff });
  sphere = new THREE.Mesh(geometry, material);
  sphere.position.x = +0;
  scene.add(sphere);
  sphereList.push(sphere);

  material = new THREE.MeshPhongMaterial({ color:0xffffff });
  sphere = new THREE.Mesh(geometry, material);
  sphere.position.x = +2;
  scene.add(sphere);
  sphereList.push(sphere);

  var directionalLight = new THREE.DirectionalLight(0xffffff);
  directionalLight.position.set(0, 0, 10);

  directionalLight.castShadow = true;
  scene.add(directionalLight);
}

function update() {
  requestAnimationFrame(update);
  const time = performance.now();

  ray.setFromCamera(mouse, camera);
  const intersects = ray.intersectObjects(sphereList);

  sphereList.map( (sphere) => {
    if (intersects.length > 0 && sphere === intersects[0].object ) {
      sphere.material.color.setHex(0xff0000);
    } else {
      sphere.material.color.setHex(0xffffff);
    }
  });
  
  console.log(controls.movementX, controls.movementY);
 
  renderer.render(scene, camera);
};


