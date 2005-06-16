var currentMenu = null;


if (!document.getElementById)
    document.getElementById = function() { return null; }

function initializeMenu(menuId, actuatorId) {
    
    var menu = document.getElementById(menuId);
    var actuator = document.getElementById(actuatorId);
    if (menu == null || actuator == null) return;
   
    actuator.onclick = function() {
        if (currentMenu == null) {
            this.showMenu();
        }
        else {
            currentMenu.style.visibility = "hidden";
            currentMenu = null;
        }

        return false;
    }
    
    
    actuator.showMenu = function() {
        menu.style.left = this.offsetLeft + "px";
        menu.style.top = this.offsetTop + this.offsetHeight + "px"; 
        menu.style.visibility = "visible";
        currentMenu = menu;
    }
}