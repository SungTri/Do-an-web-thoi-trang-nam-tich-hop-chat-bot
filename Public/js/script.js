document.addEventListener("DOMContentLoaded", function () {
    const menu = document.querySelector("nav ul");
    const toggleBtn = document.createElement("button");
    toggleBtn.classList.add("menu-toggle");
    toggleBtn.innerHTML = "☰";

    document.querySelector("header .container").prepend(toggleBtn);

    toggleBtn.addEventListener("click", function (event) {
        event.stopPropagation(); // Ngăn chặn sự kiện click lan ra ngoài
        menu.classList.toggle("active");
    });

    // Kiểm tra khi thay đổi kích thước màn hình
    function checkScreenSize() {
        if (window.innerWidth > 768) {
            toggleBtn.style.display = "none"; // Ẩn nút ☰ khi màn hình lớn
            menu.classList.remove("active"); // Đảm bảo menu không bị mở khi resize
        } else {
            toggleBtn.style.display = "block"; // Hiện nút ☰ khi màn hình nhỏ
        }
    }

    window.addEventListener("resize", checkScreenSize);
    checkScreenSize(); // Gọi hàm kiểm tra ngay khi load trang

    // Đóng menu khi nhấn vào chỗ trống
    document.addEventListener("click", function () {
        if (menu.classList.contains("active")) {
            menu.classList.remove("active");
        }
    });

    // Ngăn chặn sự kiện click lan ra ngoài menu
    menu.addEventListener("click", function (event) {
        event.stopPropagation();
    });
});
// HIỂN THỊ STORY VÀ ABOUT KHI NHẤN VÀO
document.addEventListener("DOMContentLoaded", function () {
    const storyLink = document.getElementById("story-link");
    const storySection = document.getElementById("story-section");
    const aboutLink = document.getElementById("about-link");
    const aboutSection = document.getElementById("about-section");

    storyLink.addEventListener("click", function (event) {
        event.preventDefault();
        aboutSection.style.display = "none"; // Ẩn ABOUT nếu đang mở
        if (storySection.style.display === "none" || storySection.style.display === "") {
            storySection.style.display = "block"; // Hiện STORY
        } else {
            storySection.style.display = "none"; // Ẩn STORY
        }
    });

    aboutLink.addEventListener("click", function (event) {
        event.preventDefault();
        storySection.style.display = "none"; // Ẩn STORY nếu đang mở
        if (aboutSection.style.display === "none" || aboutSection.style.display === "") {
            aboutSection.style.display = "block"; // Hiện ABOUT
        } else {
            aboutSection.style.display = "none"; // Ẩn ABOUT
        }
    });
     // Đóng ABOUT và STORIES khi nhấn vào chỗ trống
     document.addEventListener("click", function (event) {
        if (!aboutSection.contains(event.target) && !aboutLink.contains(event.target)) {
            aboutSection.style.display = "none";
        }
        if (!storySection.contains(event.target) && !storyLink.contains(event.target)) {
            storySection.style.display = "none";
        }
    });
});
// HiỂN THỊ DANH MỤC SẢN PHẨM KHI NHẤN VÀO
document.addEventListener("DOMContentLoaded", function () {
    const collectionLink = document.getElementById("collection-link");
    const dropdown = document.querySelector(".dropdown");

    collectionLink.addEventListener("click", function (event) {
        event.preventDefault(); // Ngăn chuyển trang
        dropdown.classList.toggle("active"); // Hiển thị/tắt danh mục
    });

    // Ẩn danh mục khi click bên ngoài
    document.addEventListener("click", function (event) {
        if (!dropdown.contains(event.target)) {
            dropdown.classList.remove("active");
        }
    });
});
//=======HIỂN THỊ ĐỊA CHỈ=======//
document.addEventListener("DOMContentLoaded", function () {
    const citySelect = document.getElementById("city");
    const districtSelect = document.getElementById("district");
    const wardSelect = document.getElementById("ward");
    const cityNameInput = document.getElementById("city_name");
    const districtNameInput = document.getElementById("district_name");
    const wardNameInput = document.getElementById("ward_name");

    let addressData = [];

    // Đổi đường dẫn này cho đúng với vị trí file JSON của bạn
    fetch('../../../Public/Data/vietnamAddress.json')
        .then(response => response.json())
        .then(data => {
            addressData = data;
            loadCities();
        });

    function loadCities() {
        citySelect.innerHTML = '<option value="">Chọn tỉnh / thành</option>';
        addressData.forEach(city => {
            let option = document.createElement("option");
            option.value = city.Id;
            option.textContent = city.Name;
            citySelect.appendChild(option);
        });
        cityNameInput.value = "";
        districtNameInput.value = "";
        wardNameInput.value = "";
    }

    function loadDistricts(cityId) {
        districtSelect.innerHTML = '<option value="">Chọn quận / huyện</option>';
        wardSelect.innerHTML = '<option value="">Chọn phường / xã</option>';
        let city = addressData.find(c => c.Id === cityId);
        if (city && city.Districts) {
            city.Districts.forEach(district => {
                let option = document.createElement("option");
                option.value = district.Id;
                option.textContent = district.Name;
                districtSelect.appendChild(option);
            });
            cityNameInput.value = city.Name;
        } else {
            cityNameInput.value = "";
        }
        districtNameInput.value = "";
        wardNameInput.value = "";
    }

    function loadWards(cityId, districtId) {
        wardSelect.innerHTML = '<option value="">Chọn phường / xã</option>';
        let city = addressData.find(c => c.Id === cityId);
        if (city && city.Districts) {
            let district = city.Districts.find(d => d.Id === districtId);
            if (district && district.Wards) {
                district.Wards.forEach(ward => {
                    let option = document.createElement("option");
                    option.value = ward.Id;
                    option.textContent = ward.Name;
                    wardSelect.appendChild(option);
                });
                districtNameInput.value = district.Name;
            } else {
                districtNameInput.value = "";
            }
        }
        wardNameInput.value = "";
    }

    citySelect.addEventListener("change", function () {
        loadDistricts(this.value);
    });

    districtSelect.addEventListener("change", function () {
        loadWards(citySelect.value, this.value);
    });

    wardSelect.addEventListener("change", function () {
        let city = addressData.find(c => c.Id === citySelect.value);
        let district = city?.Districts.find(d => d.Id === districtSelect.value);
        let ward = district?.Wards.find(w => w.Id === this.value);
        wardNameInput.value = ward ? ward.Name : "";
    });
});
function searchProducts() {
    const query = document.getElementById('search-query').value;
    const resultsContainer = document.getElementById('search-results');

    if (query.length > 0) {
        fetch(`../process/search_ajax.php?query=${query}`)
            .then(response => response.json())
            .then(data => {
                resultsContainer.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(product => {
                        const productItem = document.createElement('div');
                        productItem.classList.add('search-result-item');
                        productItem.innerHTML = `
                            <a href="../Pages/ProductDetail.php?id=${product.id}">
                                <img src="../${product.image_url}" alt="${product.name}">
                                <span>${product.name}</span>
                            </a>
                        `;
                        resultsContainer.appendChild(productItem);
                    });
                } else {
                    resultsContainer.innerHTML = '<p>Không tìm thấy sản phẩm nào!</p>';
                }
            })
            .catch(error => console.error('Error:', error));
    } else {
        resultsContainer.innerHTML = '';
    }
}
const pwd = document.getElementById('password');
const toggle = document.getElementById('togglePassword');
const eyeOpen = document.getElementById('eyeOpen');
const eyeClosed = document.getElementById('eyeClosed');

if (pwd && toggle && eyeOpen && eyeClosed) {
    toggle.addEventListener('click', function () {
        if (pwd.type === 'password') {
            pwd.type = 'text';
            eyeOpen.style.display = 'none';
            eyeClosed.style.display = 'inline';
        } else {
            pwd.type = 'password';
            eyeOpen.style.display = 'inline';
            eyeClosed.style.display = 'none';
        }
    });
}
document.querySelector("input[name='promo_code']").addEventListener("input", function() {
    document.getElementById("promo_code_hidden").value = this.value;
});
function confirmCancel(orderId) {
    Swal.fire({
        title: 'Bạn chắc chắn?',
        text: "Bạn có muốn hủy đơn hàng #" + orderId + " này không?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Có, hủy ngay!',
        cancelButtonText: 'Không'
    }).then((result) => {
        if (result.isConfirmed) {
            // Tạo form POST ẩn để submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../../../App/Controller/OrderController.php';

            const inputAction = document.createElement('input');
            inputAction.type = 'hidden';
            inputAction.name = 'action';
            inputAction.value = 'cancel';
            form.appendChild(inputAction);

            const inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'order_id';
            inputId.value = orderId;
            form.appendChild(inputId);

            document.body.appendChild(form);
            form.submit();
        }
    });
}
function printOrder(orderId) {
    // Mở popup mới với trang in đơn hàng
    window.open(`../../../App/Views/Admin/Print.php?id=${orderId}`, '_blank');
}