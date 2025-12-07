// Hàm thông báo thành công
function showSuccess(message, redirectUrl = null) {
    Swal.fire({
        icon: 'success',
        title: 'Thành công',
        text: message,
        confirmButtonText: 'OK',
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed && redirectUrl) {
            window.location.href = redirectUrl;
        }
    });
}

// Hàm thông báo lỗi
function showError(message, redirectUrl = null) {
    Swal.fire({
        icon: 'error',
        title: 'Lỗi',
        text: message,
        confirmButtonText: 'OK',
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed && redirectUrl) {
            window.location.href = redirectUrl;
        }
    });
}

// Hàm confirm (xác nhận) chung
function showConfirm(message, confirmText = 'Đồng ý', cancelText = 'Hủy bỏ') {
    return Swal.fire({
        title: 'Xác nhận',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText
    });
}

