const tabs = [
  { tab: document.querySelector("#tab1"), content: document.querySelector(".tab1") },
  { tab: document.querySelector("#tab2"), content: document.querySelector(".tab2") },
  { tab: document.querySelector("#tab3"), content: document.querySelector(".tab3") },
  { tab: document.querySelector("#tab4"), content: document.querySelector(".tab4") },
  { tab: document.querySelector("#tab5"), content: document.querySelector(".tab5") },
];

function activateTab(index) {
  tabs.forEach((tab, i) => {
    tab.tab.classList.toggle("active", i === index);
    tab.content.style.display = i === index ? "block" : "none";
  });
}

function active_profile() {
  activateTab(0);
}

function active_waiting() {
  activateTab(1);
}

function active_borrow() {
  activateTab(2);
}

function active_giveback() {
  activateTab(3);
}

function active_expired() {
  activateTab(4);
}

if (window.location.hash === "#tab2") {
  active_waiting();
}
//--===============================================================================================--//
// Bắt sự kiện khi trang được tải
document.addEventListener('DOMContentLoaded', function () {
  // Lấy đoạn hash từ URL
// Tạo một ánh xạ từ đoạn hash đến các hàm
var hashToFunction = {
  '#tab2': active_waiting,
  '#tab3': active_borrow,
  '#tab4': active_giveback,
  '#tab5': active_expired,
};

// Lấy đoạn hash từ URL
var hash = window.location.hash;

// Kiểm tra xem đoạn hash có phù hợp với ánh xạ không và thực hiện hàm tương ứng
if (hashToFunction.hasOwnProperty(hash)) {
  hashToFunction[hash]();
}
});

//--===============================================================================================--//
