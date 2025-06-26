// script.js

$(document).ready(function () {
    let scene, camera, renderer, cube;
    let animationId;
    let isAnimating = false;

    function initThreeJS() {
        // Create scene
        scene = new THREE.Scene();

        // Create camera
        camera = new THREE.PerspectiveCamera(
            75,
            window.innerWidth / window.innerHeight,
            0.1,
            1000
        );
        camera.position.z = 5;

        // Create renderer
        renderer = new THREE.WebGLRenderer({ alpha: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setClearColor(0x000000, 0); // transparent background
        renderer.domElement.id = 'threeCanvas';
        document.body.appendChild(renderer.domElement);

        // Create a cube
        const geometry = new THREE.BoxGeometry();
        const material = new THREE.MeshStandardMaterial({ color: 0xff6f61 });
        cube = new THREE.Mesh(geometry, material);
        scene.add(cube);

        // Add light
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
        scene.add(ambientLight);

        const pointLight = new THREE.PointLight(0xffffff, 0.8);
        pointLight.position.set(5, 5, 5);
        scene.add(pointLight);

        window.addEventListener('resize', onWindowResize, false);
    }

    function onWindowResize() {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();

        renderer.setSize(window.innerWidth, window.innerHeight);
    }

    function animate() {
        animationId = requestAnimationFrame(animate);

        cube.rotation.x += 0.01;
        cube.rotation.y += 0.01;

        renderer.render(scene, camera);
    }

    function startAnimation() {
        if (!isAnimating) {
            initThreeJS();
            animate();
            isAnimating = true;
        }
    }

    function stopAnimation() {
        if (isAnimating) {
            cancelAnimationFrame(animationId);
            renderer.domElement.remove();
            isAnimating = false;
        }
    }

    $('#startButton').click(function () {
        if (isAnimating) {
            stopAnimation();
            $(this).text('Start');
        } else {
            startAnimation();
            $(this).text('Stop');
        }
    });

    // AJAX for booking form
    $('#bookingForm').submit(function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.post('book_slot.php', formData, function (response) {
            $('#responseMessage').text(response.message);
        }, 'json');
    });

    // AJAX for report form
    $('#reportForm').submit(function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.post('report_issue.php', formData, function (response) {
            $('#responseMessage').text(response.message);
        }, 'json');
    });
});
