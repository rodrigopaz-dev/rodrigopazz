
document.addEventListener("DOMContentLoaded", () => {

   const trigger = document.getElementById("contactTrigger");
   const section = document.getElementById("contactFormSection");
   const form = document.getElementById("contactForm");
   const sendMessageBtn = document.getElementById("sendMessageBtn");

   if (!trigger || !section || !form) return;

   // Toggle contact form
   trigger.addEventListener("click", () => {

      const isOpen = section.classList.toggle("is-visible");

      trigger.setAttribute("aria-expanded", String(isOpen));
      section.setAttribute("aria-hidden", String(!isOpen));

      trigger.textContent = isOpen
         ? "Close form ×"
         : "Get in touch ↗";

      if (isOpen) {
         setTimeout(() => {
            section.scrollIntoView({
               behavior: "smooth",
               block: "start"
            });

            document.getElementById("contactName").focus({
               preventScroll: true
            });
         }, 100);
      }
   });

   // Prepare email
   form.addEventListener("submit", async (e) => {
      e.preventDefault();
      
      if (!form.reportValidity()) return;
      if (sendMessageBtn.classList.contains("is-loading")) return;

      sendMessageBtn.classList.add("is-loading");
      sendMessageBtn.disabled = true;

      try{
         const name = document.getElementById("contactName").value.trim();
         const email = document.getElementById("contactEmail").value.trim();
         const subject = document.getElementById("contactSubject").value;
         const message = document.getElementById("contactMessage").value.trim();

         const data = {};
         data["name"] = name;
         data["email"] = email;
         data["subject"] = subject;
         data["message"] = message;
         const json = JSON.stringify(data);

         console.log("Sending to server: ", json);

         const response = await fetch("https://rodrigopazz.com/send-mail.php", {
            method : "POST",
            headers: {
               "Content-Type": "application/json"
            },
            body : json
         });

         if (!response.ok) {
            throw new Error(response.text());
         }

         console.log(response);
         
         form.reset();
         alert("Your message has been sent successfully!");

      }catch(error) {
         console.log("Error sending message: ", error);
         alert("Somthing went wrong. Please try again");
      }finally {
         sendMessageBtn.classList.remove("is-loading");
         sendMessageBtn.disabled = false;
      }
   });
});
