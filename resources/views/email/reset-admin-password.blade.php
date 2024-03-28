<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password Email</title>
    <style>
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px; /* Rounded corners */
        }
    </style>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; font-size:16px; margin: 0; padding: 0;">

    <!-- Content -->
    <table width="100%" style="padding: 20px;">
        <tr>
            <td>
                <p>Hello, {{ $formData['user']->name }}</p>

                <h2 style="color: #333333;">You Have Requested to Change Your Password:</h2>
                <p>Please click the link given below to reset password.</p>

                <a href="{{ route('admin.resetPassword',$formData['token']) }}" class="button">Click Here</a>

                <p>Thanks.</p>
            </td>
        </tr>
    </table>

</body>

</html>
