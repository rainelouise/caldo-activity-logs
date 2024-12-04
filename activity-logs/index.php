<?php
require_once("./models/Applicant.php");
require_once("models/auth_check.php");

$auth = new Auth();
$auth->ensureAuthenticated(); 

$applicantObj = new Applicant();

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $username = $_SESSION['username']; 
} else {
    header("Location: login.php");
    exit();
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = htmlspecialchars($_GET['search']);
    
    $applicants = $applicantObj->searchApplicants($searchTerm, $userId);
} else {
    $applicants = $applicantObj->getAllApplicants($userId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Management - All Applicants</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function confirmDelete(applicantId) {
            if (confirm("Are you sure you want to delete this applicant?")) {
                document.getElementById('deleteForm' + applicantId).submit();
            }
        }
    </script>
</head>
<body class="bg-pink-50">

<div class="container mx-auto p-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-4xl font-semibold text-center text-pink-600">Applicants List</h1>

        <div class="flex items-center space-x-4">
            <span class="text-gray-700 font-semibold">Hello, <?php echo htmlspecialchars($username); ?>!</span>
            <form action="action/process_logout.php" method="POST" class="inline-block">
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-600 transition">Logout</button>
            </form>
        </div>
    </div>

    <a href="create_applicant.php" class="bg-pink-500 text-white px-4 py-2 rounded-full hover:bg-pink-600 transition mb-4 inline-block">Add a New Applicant</a>
    <a href="activity_logs.php" class="bg-yellow-500 text-white px-4 py-2 rounded-full hover:bg-yellow-600 transition ml-4 inline-block">Activity Logs</a>

    <form action="index.php" method="GET" class="mb-4 inline-block">
        <input type="text" name="search" placeholder="Search by name or username" class="px-4 py-2 border border-pink-300 rounded-full mr-2 focus:ring-2 focus:ring-pink-500" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 focus:ring-2 focus:ring-blue-400 transition">Search</button>
    </form>

    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
            <p><?php echo htmlspecialchars($_GET['success']); ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($applicants) && !empty($applicants)): ?>
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-lg">
            <thead>
                <tr class="bg-pink-100 text-gray-700">
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Username</th>
                    <th class="px-4 py-2 text-left">First Name</th>
                    <th class="px-4 py-2 text-left">Last Name</th>
                    <th class="px-4 py-2 text-left">Birth Date</th>
                    <th class="px-4 py-2 text-left">Gender</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Phone</th>
                    <th class="px-4 py-2 text-left">Applied Position</th>
                    <th class="px-4 py-2 text-left">Start Date</th>
                    <th class="px-4 py-2 text-left">Address</th>
                    <th class="px-4 py-2 text-left">Nationality</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applicants as $applicant): ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?php echo htmlspecialchars($applicant['id']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($applicant['username']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($applicant['first_name']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($applicant['last_name']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($applicant['birth_date']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($applicant['gender']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($applicant['email_address']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($applicant['phone_number']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($applicant['applied_position']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($applicant['start_date']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($applicant['address']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($applicant['nationality']); ?></td>
                        <td class="px-4 py-2">
                            <a href="edit_applicant.php?id=<?php echo $applicant['id']; ?>" class="text-blue-500 hover:text-blue-700">Update</a>
                            
                            <form id="deleteForm<?php echo $applicant['id']; ?>" action="action/process_delete_applicant.php" method="POST" class="inline-block">
                                <input type="hidden" name="id" value="<?php echo $applicant['id']; ?>">
                                <button type="button" onclick="confirmDelete(<?php echo $applicant['id']; ?>)" class="text-red-500 hover:text-red-700 ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-gray-700">No applicants found.</p>
    <?php endif; ?>
</div>

</body>
</html>