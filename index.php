<?php
// Express.js API URL
$api_url = "http://localhost:5000/api"; 

// Fetch data from API
$regionData = json_decode(file_get_contents("$api_url/regions"), true);
$serviceData = json_decode(file_get_contents("$api_url/services"), true);
$callTypeData = json_decode(file_get_contents("$api_url/call-types"), true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AWS Cost Calculation</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; display: none; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #007BFF; color: white; }
        .total-cost { font-weight: bold; background-color: #f8f9fa; }
    </style>
</head>
<body>

    <h2>AWS Cost Calculation</h2>

    <form id="calculationForm">
        <!-- AWS Region Selection -->
        <label for="region">AWS Region:</label>
        <select id="region" name="region_id">
            <option value="">Select a region</option>
            <?php foreach ($regionData as $row): ?>
                <option value="<?= $row['region_id']; ?>"><?= $row['region_name']; ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Country Selection -->
        <label for="country">Country:</label>
        <select id="country" name="country_id" disabled>
            <option value="">Select a country</option>
        </select>

        <!-- Service Selection -->
        <label for="service">Service:</label>
        <select id="service" name="service_id">
            <?php foreach ($serviceData as $row): ?>
                <option value="<?= $row['service_id']; ?>"><?= $row['service_name']; ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Call Type Selection -->
        <label for="call_type">Call Type:</label>
        <select id="call_type" name="call_type_id">
            <?php foreach ($callTypeData as $row): ?>
                <option value="<?= $row['call_type_id']; ?>"><?= $row['call_type_name']; ?></option>
            <?php endforeach; ?>
        </select>

        <!-- User Inputs -->
        <label for="daily_usage">Daily Usage:</label>
        <input type="number" id="daily_usage" name="daily_usage" value="15" required>

        <label for="business_days">Business Days:</label>
        <input type="number" id="business_days" name="business_days" value="30" required>

        <label for="aht">AHT (Avg Handle Time in Minutes):</label>
        <input type="number" step="0.01" id="aht" name="aht" value="5.00" required>

        <button type="submit">Calculate</button>
    </form>

    <h3>Calculation Results</h3>

    <table id="resultsTable">
        <thead>
            <tr>
                <th></th>
                <th>Country Code</th>
                <th>Daily</th>
                <th>Business Days</th>
                <th>Monthly Calls</th>
                <th>AHT (mins)</th>
                <th>Call Mins</th>
                <th>Charges per Min.</th>
                <th>Costs (In USD)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Cost (Voice Usage)</td>
                <td id="country_code"></td>
                <td id="daily_usage_value"></td>
                <td id="business_days_value"></td>
                <td id="monthly_calls"></td>
                <td id="aht_value"></td>
                <td id="call_minutes"></td>
                <td id="voice_usage"></td>
                <td id="cost_voice_usage"></td>
            </tr>
            <tr>
                <td>Cost (DID)</td>
                <td colspan="6"></td>
                <td id="DID_rate"></td>
                <td id="cost_did"></td>
            </tr>
            <tr>
                <td>Cost (Toll-Free)</td>
                <td colspan="6"></td>
                <td id="toll_free_rate"></td>
                <td id="cost_toll_free"></td>
            </tr>
            <tr class="total-cost">
                <td>Total Cost</td>
                <td colspan="7"></td>
                <td id="total_cost"></td>
            </tr>
        </tbody>
    </table>
    <script>
    $(document).ready(function() {
        // Load Countries when Region is selected
        $('#region').change(function() {
            let regionId = $(this).val();
            if (regionId) {
                $.post("fetch_data.php", { action: "get_countries", region_id: regionId }, function(data) {
                    $('#country').html('<option value="">Select a country</option>');
                    $.each(data, function(index, country) {
                        $('#country').append(`<option value="${country.country_id}">${country.country_name}</option>`);
                    });
                    $('#country').prop('disabled', false);
                }, "json");
            }
        });

        // Handle Form Submission
        $("#calculationForm").submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: "calculate.php",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(response) {
                    if (response.error) {
                        alert("Error: " + response.error);
                    } else {
                        $("#resultsTable").show(); // Show table when data is available
                        $("#country_code").text(response.country_code);
                        $("#daily_usage_value").text(response.daily_usage);
                        $("#business_days_value").text(response.business_days);
                        $("#monthly_calls").text(response.monthly_calls);
                        $("#aht_value").text(response.aht);
                        $("#call_minutes").text(response.call_minutes);

                        $("#voice_usage").text(response.voice_usage);
                        $("#DID_rate").text(response.DID_rate);
                        $("#toll_free_rate").text(response.toll_free_rate);

                        $("#cost_voice_usage").text(`$${parseFloat(response.cost_voice_usage).toFixed(4)}`);
                        $("#cost_did").text(`$${parseFloat(response.cost_did).toFixed(4)}`);
                        $("#cost_toll_free").text(`$${parseFloat(response.cost_toll_free).toFixed(4)}`);
                        $("#total_cost").text(`$${parseFloat(response.total_cost).toFixed(4)}`);
                    }
                },
                error: function(xhr, status, error) {
                    alert("AJAX Error: " + error);
                }
            });
        });
    });
</script>


</body>
</html>
