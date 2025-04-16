let scene, camera, renderer, jerseyModel, textureCanvas, textureContext, texture;

function init() {
  // Scene setup
  scene = new THREE.Scene();
  camera = new THREE.PerspectiveCamera(75, 0.7, 0.1, 1000);
  camera.position.z = 5;

  renderer = new THREE.WebGLRenderer({ antialias: true });
  renderer.setSize(window.innerWidth * 0.7, 600);
  document.getElementById('jersey-viewer').appendChild(renderer.domElement);

  // Light
  const light = new THREE.AmbientLight(0xffffff, 1);
  scene.add(light);

  // Texture canvas
  textureCanvas = document.createElement('canvas');
  textureCanvas.width = 1024;
  textureCanvas.height = 1024;
  textureContext = textureCanvas.getContext('2d');

  // Load base texture
  const baseImage = new Image();
  baseImage.src = 'assets/textures/base_texture.png';
  baseImage.onload = () => {
    textureContext.drawImage(baseImage, 0, 0, 1024, 1024);
    texture = new THREE.CanvasTexture(textureCanvas);
    loadModel();
  };

  // Event listeners
  document.getElementById('applyBtn').addEventListener('click', updateTexture);
}

function loadModel() {
  const loader = new THREE.GLTFLoader();
  loader.load('assets/jersey.glb', (gltf) => {
    jerseyModel = gltf.scene;
    jerseyModel.traverse((child) => {
      if (child.isMesh) {
        child.material.map = texture;
        child.material.needsUpdate = true;
      }
    });
    scene.add(jerseyModel);
    animate();
  });
}

function updateTexture() {
  // Clear canvas
  textureContext.clearRect(0, 0, 1024, 1024);

  // Redraw base texture
  const baseImage = new Image();
  baseImage.src = 'assets/textures/base_texture.png';
  baseImage.onload = () => {
    textureContext.drawImage(baseImage, 0, 0, 1024, 1024);

    // Get user inputs
    const color = document.getElementById('colorPicker').value;
    const text = document.getElementById('textInput').value;

    // Apply color overlay
    textureContext.fillStyle = color;
    textureContext.globalAlpha = 0.5;
    textureContext.fillRect(0, 0, 1024, 1024);
    textureContext.globalAlpha = 1.0;

    // Add text
    textureContext.fillStyle = '#000';
    textureContext.font = 'bold 100px Arial';
    textureContext.textAlign = 'center';
    textureContext.fillText(text, 512, 512);

    // Update texture
    texture.needsUpdate = true;
  };
}

function animate() {
  requestAnimationFrame(animate);
  if (jerseyModel) {
    jerseyModel.rotation.y += 0.005;
  }
  renderer.render(scene, camera);
}

init();
