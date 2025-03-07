


const pages = {
    "Vehicle": "Vehicle.php",
    "Failue Type": "failuer_types.php",
    "Failure Events": "Failure_Events.php",
    "Sensors": "Sensors.php",
    "Vehicle-Sensors": "vehicle_sensors.php",
    "Sensor-Data": "sensor_data.php",
    "Test-Scenarios": "Test_Scenarios.php",
    "Vehicle Tests": "Vehicle_Test.php",
    "Success-Criteria": "Success_Criteria.php",
    "Test-Success": "Test_Success.php",
    "Enviroment Factors": "Environment_Factors.php",
    "Driver Interventions": "Driver_Interventions.php",
    "Safety-Issues": "safety_issues.php",
    "Software Versions": "Software_Versions.php",
    "Maintenance Records": "Maintenance_Records.php",
    "Attack Type": "attacktype.php",
    "Vehicle-Status": "vehicle_status.php",
    "Software Updates": "Software_Updates.php",
    "Personnal": "Personnel.php",
    "Test-Personnal 1": "Test-Personnal.php"
};

function searchPage() {
    const searchTerm = document.getElementById("search-bar").value;
    if (pages[searchTerm]) {
        window.location.href = pages[searchTerm]; // Redirect to the page
    } else {
        alert("Page not found!");
    }
}
