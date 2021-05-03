<html>
<head><title>Instance Information</title></head>
<body>
<?php
function InstanceMetaData($title, $parameter) {
		$URL = "http://169.254.169.254/latest/meta-data/" . $parameter;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$URL");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $output = '';

        if (!curl_errno($ch)) {
		  switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
		    case 200:
		    $output = $title . $result . '</br>';
		    break;
		    #default:
		    #$output = $http_code ;
		    #break;
  		}
  	}
        
    curl_close($ch);
    return $output;
}

$output = InstanceMetaData("Instance ID: ", "instance-id");
$output .= InstanceMetaData("Instance Type: ", "instance-type");
$output .= InstanceMetaData("Availability Zone: ", "placement/availability-zone");
$output .= InstanceMetaData("Local IPv4: ", "local-ipv4");
$output .= InstanceMetaData("AMI ID: ", "ami-id");
$output .= InstanceMetaData("AMI Lunch Index: ", "ami-launch-index");
$output .= InstanceMetaData("AMI Manifest Path: ", "ami-manifest-path");
$output .= InstanceMetaData("Ancestor Ami IDs: ", "ancestor-ami-ids");
$output .= InstanceMetaData("Block Device Mapping-AMI: ", "block-device-mapping/ami");
$output .= InstanceMetaData("Block Device Mapping-EBSN: ", "block-device-mapping/ebsN");
$output .= InstanceMetaData("Block Device Mapping-EphemeralN: ", "block-device-mapping/ephemeralN");
$output .= InstanceMetaData("Block Device Mapping-Root: ", "block-device-mapping/root");
$output .= InstanceMetaData("Block Device Mapping-Swap: ","block-device-mapping/swap");
$output .= InstanceMetaData("Elastic GPU ID: ", "elastic-gpus/associations/elastic-gpu-id");
$output .= InstanceMetaData("EIA ID: ", "elastic-inference/associations/eia-id");
$output .= InstanceMetaData("Maintenance History: ", "events/maintenance/history");
$output .= InstanceMetaData("Maintenance Scheduled: ", "events/maintenance/scheduled");
$output .= InstanceMetaData("Host Name: ", "hostname");
$output .= InstanceMetaData("AMI Info: ", "iam/info");
$output .= InstanceMetaData("Role Name: ", "iam/security-credentials/role-name");
$output .= InstanceMetaData("EC2 Info: ", "identity-credentials/ec2/info");
#$output .= InstanceMetaData("Security Credentials: ", "identity-credentials/ec2/security-credentials/ec2-instance");
$output .= InstanceMetaData("Instance Action: ", "instance-action");
$output .= InstanceMetaData("Kernel ID: ", "kernel-id");
$output .= InstanceMetaData("Local Hostname: ", "local-hostname");
$output .= InstanceMetaData("MAC: ", "mac");
$output .= InstanceMetaData("Network Interface Device Number: ", "network/interfaces/macs/mac/device-number");
$output .= InstanceMetaData("Interface ID: ", "network/interfaces/macs/mac/interface-id");
$output .= InstanceMetaData("Public IP: ", "network/interfaces/macs/mac/ipv4-associations/public-ip");
$output .= InstanceMetaData("IPv6: ", "network/interfaces/macs/mac/ipv6s");
$output .= InstanceMetaData("Local Hostname: ", "network/interfaces/macs/mac/local-hostname");
$output .= InstanceMetaData("Local IPv4: ", "network/interfaces/macs/mac/local-ipv4s");
$output .= InstanceMetaData("Network Interface MAC: ", "network/interfaces/macs/mac/mac");
$output .= InstanceMetaData("Owner ID: ", "network/interfaces/macs/mac/owner-id");
$output .= InstanceMetaData("Public Hostname: ", "network/interfaces/macs/mac/public-hostname");
$output .= InstanceMetaData("Public IPv4: ", "network/interfaces/macs/mac/public-ipv4s");
$output .= InstanceMetaData("Security Group: ", "network/interfaces/macs/mac/security-groups");
$output .= InstanceMetaData("Security Group ID: ", "network/interfaces/macs/mac/security-group-ids");
$output .= InstanceMetaData("Subnet ID: ", "network/interfaces/macs/mac/subnet-id");
$output .= InstanceMetaData("Subnet IPv4 CIDR Block: ", "network/interfaces/macs/mac/subnet-ipv4-cidr-block");
$output .= InstanceMetaData("Subnet IPv6 CIDR Block: ", "network/interfaces/macs/mac/subnet-ipv6-cidr-blocks");
$output .= InstanceMetaData("VPC ID: ", "network/interfaces/macs/mac/vpc-id");
$output .= InstanceMetaData("VPC IPv4 CIDR Block: ", "network/interfaces/macs/mac/vpc-ipv4-cidr-block");
$output .= InstanceMetaData("VPC IPv4 CIDR Blocks: ", "network/interfaces/macs/mac/vpc-ipv4-cidr-blocks");
$output .= InstanceMetaData("VPC IPv6 CIDR Blocks: ", "network/interfaces/macs/mac/vpc-ipv6-cidr-blocks");
$output .= InstanceMetaData("Product Code: ", "product-codes");
$output .= InstanceMetaData("Public Hostname: ", "public-hostname");
$output .= InstanceMetaData("Public IPv4: ", "public-ipv4");
#$output .= InstanceMetaData("OpenSSH Key: ", "public-keys/0/openssh-key");
$output .= InstanceMetaData("RAM Disk ID: ", "ramdisk-id");
$output .= InstanceMetaData("Reservation ID: ", "reservation-id");
$output .= InstanceMetaData("Security Group: ", "security-groups");
$output .= InstanceMetaData("Domain: " , "services/domain");
$output .= InstanceMetaData("Partition: ", "services/partition");
$output .= InstanceMetaData("Instance Action: ", "spot/instance-action");
$output .= InstanceMetaData("Termination Time: ", "spot/termination-time");

echo '<pre style="word-wrap: break-word;">' . $output. '</pre>';
?>
</body>