<!DOCTYPE html>
<html lang="en">
<head>
    <title>AWS Cost Calculation</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #007BFF; color: white; }
        .total-cost { font-weight: bold; background-color: #f8f9fa; }
    </style>
</head>
<body>
    <h2>AWS Cost Calculation</h2>

    <form id="calculationForm">
        <!-- Region Dropdown -->
        <label for="region">Region:</label>
<select id="region" name="region_id">
    <option value="">Select Region</option>
</select><br><br>

<label for="country">Country:</label>
<select id="country" name="country_id">
    <option value="">Select Country</option>
</select><br><br>

        <!-- Service Dropdown -->
        <label for="service">Service:</label>
        <select id="service" name="service_id">
            <option value="">Select Service</option>
        </select><br><br>

        <!-- Call Type Dropdown -->
        <label for="call_type">Call Type:</label>
        <select id="call_type" name="call_type_id">
            <option value="">Select Call Type</option>
        </select><br><br>

        <!-- User Inputs -->
        <label for="daily_usage">Daily Usage:</label>
        <input type="number" id="daily_usage" name="daily_usage" value="15" required><br><br>

        <label for="business_days">Business Days:</label>
        <input type="number" id="business_days" name="business_days" value="30" required><br><br>

        <label for="aht">AHT (Avg Handle Time in Minutes):</label>
        <input type="number" step="0.01" id="aht" name="aht" value="5.00" required><br><br>

        <button type="submit">Calculate</button>
    </form>

    <h3>Calculation Results</h3>

    <table id="resultsTable" style="display: none;">
        <thead>
            <tr>
                <th>Cost Type</th>
                <th>Selected Country</th>
                <th>Daily Usage</th>
                <th>Business Days</th>
                <th>Monthly Calls</th>
                <th>AHT (mins)</th>
                <th>Call Mins</th>
                <th>Cost</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows for Voice Usage, DID, and Toll Free -->
            <tr id="voice_usage_row">
                <td>Cost (Voice Usage)</td>
                <td id="country_name"></td>
                <td id="daily_usage_value"></td>
                <td id="business_days_value"></td>
                <td id="monthly_calls"></td>
                <td id="aht_value"></td>
                <td id="call_minutes"></td>
                <td id="cost_voice_usage"></td>
            </tr>
            <tr id="did_row">
                <td>Cost (DID)</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td id="cost_did"></td>
            </tr>
            <tr id="toll_free_row">
                <td>Cost (Toll Free)</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td id="cost_toll_free"></td>
            </tr>
            <tr id="total_cost_row">
                <td>Total Cost</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td id="total_cost"></td>
            </tr>
        </tbody>
    </table>

    <script>
        $(document).ready(function () {
            // Fetch regions on page load
            $.post("fetch_data.php", { action: "get_regions" }, function (response) {
                response.forEach(region => {
                    $("#region").append(`<option value="${region.region_id}">${region.region_name}</option>`);
                });
            }, "json");

            // Fetch countries when region is selected
            $("#region").change(function () {
    const regionId = $(this).val();
    $.post("fetch_data.php", { action: "get_countries", region_id: regionId }, function (response) {
        $("#country").html('<option value="">Select Country</option>');
        response.forEach(country => {
            $("#country").append(`<option value="${country.country_id}">${country.country_name}</option>`);
        });
    }, "json");
});

            // Fetch services on page load
            $.post("fetch_data.php", { action: "get_services" }, function (response) {
                response.forEach(service => {
                    $("#service").append(`<option value="${service.service_id}">${service.service_name}</option>`);
                });
            }, "json");

            // Fetch call types when service is selected
            $("#service").change(function () {
                const serviceId = $(this).val();
                $.post("fetch_data.php", { action: "get_call_types", service_id: serviceId }, function (response) {
                    $("#call_type").html('<option value="">Select Call Type</option>');
                    response.forEach(callType => {
                        $("#call_type").append(`<option value="${callType.call_type_id}">${callType.call_type_name}</option>`);
                    });
                }, "json");
            });

            // Handle form submission
            $("#calculationForm").submit(function (e) {
                e.preventDefault();

                $.post("calculate.php", $(this).serialize(), function (response) {
                    $("#resultsTable").show();

                    // Populate table with response data
                    $("#country_name").text(response.country_name);
                    $("#daily_usage_value").text(response.daily_usage);
                    $("#business_days_value").text(response.business_days);
                    $("#monthly_calls").text(response.monthly_calls);
                    $("#aht_value").text(response.aht);
                    $("#call_minutes").text(response.call_minutes);

                    $("#cost_voice_usage").text("$" + parseFloat(response.cost_voice_usage).toFixed(4));
                    $("#cost_did").text("$" + parseFloat(response.cost_did).toFixed(4));
                    $("#cost_toll_free").text("$" + parseFloat(response.cost_toll_free).toFixed(4));
                    $("#total_cost").text("$" + parseFloat(response.total_cost).toFixed(4));
                }, "json");
            });
        });
    </script>
</body>
</html>