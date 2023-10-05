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
  