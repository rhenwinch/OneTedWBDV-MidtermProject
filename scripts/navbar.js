
try {
	const drawerToggle = document.getElementById("drawer-toggle");
	const drawer = document.getElementById("drawer");
	const overlay = document.getElementById("overlay");

	drawerToggle.addEventListener("click", () => {
		console.log("Clicked!");
		drawer.classList.toggle("open");
		overlay.classList.toggle("show");
	});

	overlay.addEventListener("click", () => {
		drawer.classList.toggle("open");
		overlay.classList.toggle("show");
	});
} catch (error) {}
