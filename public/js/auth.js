const images = [
    "/images/slide1.png",
    "/images/slide2.png",
    "/images/slide3.png",
    "/images/slide4.png",
    "/images/slide5.png"
];

const descriptions = [
    {
        title: "Choose the Perfect Ride",
        text: "Sedans, SUVs, and premium cars all ready for your next trip."
    },
    {
        title: "Book in Minutes",
        text: "Reserve your vehicle anytime, anywhere and hassle-free."
    },
    {
        title: "Drive With Confidence",
        text: "Every vehicle is inspected and maintained for safe, smooth travels."
    },
    {
        title: "Fair Prices, No Hidden Fees",
        text: "Clear pricing for every type of traveler."
    },
    {
        title: "Where Your Journey Begins",
        text: "A scenic Luzon route perfect for road trips, with your car leading the way."
    }
];

let currentIndex = localStorage.getItem('currentImageIndex') 
  ? parseInt(localStorage.getItem('currentImageIndex')) 
  : 0;

const imageElement = document.getElementById("showcase-image");
const descElement = document.getElementById("imageDescription");
const dots = document.querySelectorAll(".dot");

descElement.textContent = descriptions[currentIndex].title + " — " + descriptions[currentIndex].text;

function nextImage() {
    currentIndex = (currentIndex + 1) % images.length;
    updateImage();
}

function prevImage() {
    currentIndex = (currentIndex - 1 + images.length) % images.length;
    updateImage();
}

function updateImage() {
    imageElement.style.opacity = 0;

    setTimeout(() => {
        imageElement.src = images[currentIndex];

        descElement.textContent =
        descriptions[currentIndex].title + " — " + descriptions[currentIndex].text;

        imageElement.style.opacity = 1;
    }, 10);

    dots.forEach((dot, i) => {
        dot.classList.toggle("bg-green-500", i === currentIndex);
        dot.classList.toggle("bg-gray-400", i !== currentIndex);
    });

    localStorage.setItem('currentImageIndex', currentIndex);
}

const nextBtn = document.getElementById("nextBtn");
const prevBtn = document.getElementById("prevBtn");

if (nextBtn) {
  nextBtn.addEventListener("click", nextImage);
}

if (prevBtn) {
  prevBtn.addEventListener("click", prevImage);
}

// Make dots clickable
dots.forEach((dot, i) => {
  dot.addEventListener("click", () => {
    currentIndex = i;
    updateImage();
  });
});

updateImage();

setInterval(nextImage, 4000);

function togglePassword(fieldId, element) {
    const input = document.getElementById(fieldId);
    const icon = element.querySelector('i');
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

const togglePasswordLoginBtn = document.getElementById('togglePassword');
if (togglePasswordLoginBtn) {
  togglePasswordLoginBtn.addEventListener('click', function() {
    togglePassword('password', this);
  });
}

function showPasswordHint() {
  document.getElementById('passwordHint').style.display = 'block';
}

function hidePasswordHint() {
  const hint = document.getElementById('passwordHint');
  const password = document.getElementById('password').value;
  if (password === '' || allRulesPassed(password)) {
    hint.style.display = 'none';
  }
}

function validatePassword(password) {
  const lenRule = document.getElementById('lenRule');
  const upperRule = document.getElementById('upperRule');
  const numRule = document.getElementById('numRule');
  const spaceRule = document.getElementById('spaceRule');
  updateRule(lenRule, password.length >= 8 && password.length <= 20);
  updateRule(upperRule, /[A-Z]/.test(password));
  updateRule(numRule, /\d/.test(password));
  updateRule(spaceRule, !/\s/.test(password));

  document.getElementById('passwordHint').style.display = allRulesPassed(password) ? 'none' : 'block';
}

function updateRule(element, isValid) {
  const icon = element.querySelector('i');
  if (isValid) {
    icon.classList.replace('fa-xmark', 'fa-check');
    icon.classList.replace('text-red-500', 'text-green-500');
    element.classList.add('text-green-600');
  } else {
    icon.classList.replace('fa-check', 'fa-xmark');
    icon.classList.replace('text-green-500', 'text-red-500');
    element.classList.remove('text-green-600');
  }
}

function allRulesPassed(password) {
  return password.length >= 8 && password.length <= 20 &&
        /[A-Z]/.test(password) && /\d/.test(password) &&
        !/\s/.test(password);
}
