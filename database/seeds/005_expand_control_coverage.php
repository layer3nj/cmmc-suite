<?php

/**
 * Expand control coverage to reach 110 controls for CMMC ML2 and NIST 800-171
 * This adds the missing controls from the complete frameworks
 */

return function($db) {
    $controls = [
        // Additional CMMC ML2 Access Control (AC) domain
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.7', 'title' => 'Prevent non-privileged users from executing privileged functions', 'description' => 'Prevent non-privileged users from executing privileged functions and audit the execution of such functions.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.8', 'title' => 'Limit unsuccessful logon attempts', 'description' => 'Limit unsuccessful logon attempts.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.9', 'title' => 'Provide privacy and security notices', 'description' => 'Provide privacy and security notices consistent with applicable CUI rules.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.10', 'title' => 'Use session lock with pattern-hiding displays', 'description' => 'Use session lock with pattern-hiding displays to prevent access and viewing of data after a period of inactivity.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.11', 'title' => 'Terminate sessions after a defined time period of inactivity', 'description' => 'Terminate (automatically) a user session after a defined condition.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.12', 'title' => 'Monitor and control remote access sessions', 'description' => 'Monitor and control remote access sessions.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.13', 'title' => 'Employ cryptographic mechanisms for remote access', 'description' => 'Employ cryptographic mechanisms to protect the confidentiality of remote access sessions.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.14', 'title' => 'Route remote access via managed access control points', 'description' => 'Route remote access via managed access control points.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.15', 'title' => 'Authorize remote execution of privileged commands', 'description' => 'Authorize remote execution of privileged commands and remote access to security-relevant information.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.16', 'title' => 'Authorize wireless access prior to connection', 'description' => 'Authorize wireless access prior to allowing such connections.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.17', 'title' => 'Protect wireless access using authentication and encryption', 'description' => 'Protect wireless access using authentication and encryption.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.18', 'title' => 'Control connection of mobile devices', 'description' => 'Control connection of mobile devices.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.19', 'title' => 'Encrypt CUI on mobile devices', 'description' => 'Encrypt CUI on mobile devices and mobile computing platforms.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.21', 'title' => 'Limit use of portable storage devices', 'description' => 'Limit use of organizational information system portable storage devices on external information systems.', 'ml_level' => 2],

        // Configuration Management (CM) domain
        ['framework' => 'CMMC', 'code' => 'CM.L2-3.4.1', 'title' => 'Establish and maintain baseline configurations', 'description' => 'Establish and maintain baseline configurations and inventories of organizational information systems.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'CM.L2-3.4.2', 'title' => 'Establish and enforce security configuration settings', 'description' => 'Establish and enforce security configuration settings for IT products employed in organizational information systems.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'CM.L2-3.4.3', 'title' => 'Track and control changes to systems', 'description' => 'Track, review, approve/disapprove, and audit changes to organizational information systems.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'CM.L2-3.4.4', 'title' => 'Analyze security impact of changes prior to implementation', 'description' => 'Analyze the security impact of changes prior to implementation.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'CM.L2-3.4.5', 'title' => 'Define and enforce access restrictions for change', 'description' => 'Define, document, approve, and enforce physical and logical access restrictions associated with changes to organizational information systems.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'CM.L2-3.4.6', 'title' => 'Employ least functionality', 'description' => 'Employ the principle of least functionality by configuring organizational information systems to provide only essential capabilities.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'CM.L2-3.4.7', 'title' => 'Restrict and monitor use of nonessential programs', 'description' => 'Restrict, disable, and prevent the use of nonessential programs, functions, ports, protocols, and services.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'CM.L2-3.4.8', 'title' => 'Apply deny-by-exception policy to software programs', 'description' => 'Apply deny-by-exception policy to prevent the use of unauthorized software or deny-all, permit-by-exception policy to allow the execution of authorized software.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'CM.L2-3.4.9', 'title' => 'Control and monitor user-installed software', 'description' => 'Control and monitor user-installed software.', 'ml_level' => 2],

        // Identification and Authentication (IA) domain
        ['framework' => 'CMMC', 'code' => 'IA.L2-3.5.3', 'title' => 'Use multifactor authentication for local and network access', 'description' => 'Use multifactor authentication for local and network access to privileged accounts and for network access to non-privileged accounts.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'IA.L2-3.5.4', 'title' => 'Employ replay-resistant authentication mechanisms', 'description' => 'Employ replay-resistant authentication mechanisms for network access to privileged and non-privileged accounts.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'IA.L2-3.5.5', 'title' => 'Prevent reuse of identifiers', 'description' => 'Prevent reuse of identifiers for a defined period.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'IA.L2-3.5.6', 'title' => 'Disable identifiers after a defined period of inactivity', 'description' => 'Disable identifiers after a defined period of inactivity.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'IA.L2-3.5.7', 'title' => 'Enforce minimum password complexity and change requirements', 'description' => 'Enforce a minimum password complexity and change of characters when new passwords are created.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'IA.L2-3.5.8', 'title' => 'Prohibit password reuse for a specified number of generations', 'description' => 'Prohibit password reuse for a specified number of generations.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'IA.L2-3.5.9', 'title' => 'Allow temporary password use for system logons', 'description' => 'Allow temporary password use for system logons with an immediate change to a permanent password.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'IA.L2-3.5.10', 'title' => 'Store and transmit only cryptographically-protected passwords', 'description' => 'Store and transmit only cryptographically-protected passwords.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'IA.L2-3.5.11', 'title' => 'Obscure feedback of authentication information', 'description' => 'Obscure feedback of authentication information.', 'ml_level' => 2],

        // Incident Response (IR) domain
        ['framework' => 'CMMC', 'code' => 'IR.L2-3.6.1', 'title' => 'Establish an incident response capability', 'description' => 'Establish an operational incident-handling capability for organizational information systems that includes adequate preparation, detection, analysis, containment, recovery, and user response activities.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'IR.L2-3.6.2', 'title' => 'Track, document, and report incidents', 'description' => 'Track, document, and report incidents to appropriate officials and/or authorities both internal and external to the organization.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'IR.L2-3.6.3', 'title' => 'Test incident response capability', 'description' => 'Test the organizational incident response capability.', 'ml_level' => 2],

        // Maintenance (MA) domain
        ['framework' => 'CMMC', 'code' => 'MA.L2-3.7.1', 'title' => 'Perform maintenance on systems', 'description' => 'Perform maintenance on organizational information systems.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'MA.L2-3.7.2', 'title' => 'Provide controls on tools used for system maintenance', 'description' => 'Provide controls on the tools, techniques, mechanisms, and personnel used to conduct information system maintenance.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'MA.L2-3.7.3', 'title' => 'Ensure equipment removed for maintenance is sanitized', 'description' => 'Ensure equipment removed for off-site maintenance is sanitized of any CUI.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'MA.L2-3.7.4', 'title' => 'Check media containing diagnostic programs for malicious code', 'description' => 'Check media containing diagnostic and test programs for malicious code before the media are used in organizational information systems.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'MA.L2-3.7.5', 'title' => 'Require multifactor authentication for remote maintenance', 'description' => 'Require multifactor authentication to establish nonlocal maintenance sessions via external network connections and terminate such connections when nonlocal maintenance is complete.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'MA.L2-3.7.6', 'title' => 'Supervise maintenance activities by personnel without security clearances', 'description' => 'Supervise the maintenance activities of maintenance personnel without required access authorization.', 'ml_level' => 2],

        // Media Protection (MP) domain
        ['framework' => 'CMMC', 'code' => 'MP.L2-3.8.1', 'title' => 'Protect system media', 'description' => 'Protect (i.e., physically control and securely store) information system media containing CUI, both paper and digital.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'MP.L2-3.8.2', 'title' => 'Limit access to CUI on system media', 'description' => 'Limit access to CUI on information system media to authorized users.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'MP.L2-3.8.4', 'title' => 'Mark media with CUI markings', 'description' => 'Mark media with necessary CUI markings and distribution limitations.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'MP.L2-3.8.5', 'title' => 'Control access to media during transport', 'description' => 'Control access to media containing CUI and maintain accountability for media during transport outside of controlled areas.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'MP.L2-3.8.6', 'title' => 'Implement cryptographic mechanisms for media', 'description' => 'Implement cryptographic mechanisms to protect the confidentiality of CUI stored on digital media during transport unless otherwise protected by alternative physical safeguards.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'MP.L2-3.8.7', 'title' => 'Control use of removable media on system components', 'description' => 'Control the use of removable media on information system components.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'MP.L2-3.8.8', 'title' => 'Prohibit use of portable storage devices', 'description' => 'Prohibit the use of portable storage devices when such devices have no identifiable owner.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'MP.L2-3.8.9', 'title' => 'Protect backups of CUI', 'description' => 'Protect the confidentiality of backup CUI at storage locations.', 'ml_level' => 2],

        // Personnel Security (PS) domain
        ['framework' => 'CMMC', 'code' => 'PS.L2-3.9.1', 'title' => 'Screen individuals prior to authorizing access', 'description' => 'Screen individuals prior to authorizing access to organizational information systems containing CUI.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'PS.L2-3.9.2', 'title' => 'Ensure CUI handling requirements are followed', 'description' => 'Ensure that organizational information and information systems are protected during and after personnel actions such as terminations and transfers.', 'ml_level' => 2],

        // Physical Protection (PE) domain
        ['framework' => 'CMMC', 'code' => 'PE.L2-3.10.2', 'title' => 'Protect and monitor physical facility and support infrastructure', 'description' => 'Protect and monitor the physical facility and support infrastructure for organizational information systems.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'PE.L2-3.10.6', 'title' => 'Enforce safeguarding measures for CUI', 'description' => 'Enforce safeguarding measures for CUI at alternate work sites.', 'ml_level' => 2],

        // Risk Assessment (RA) domain
        ['framework' => 'CMMC', 'code' => 'RA.L2-3.11.1', 'title' => 'Conduct risk assessments periodically', 'description' => 'Periodically assess the risk to organizational operations, organizational assets, and individuals, resulting from the operation of organizational information systems and the associated processing, storage, or transmission of CUI.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'RA.L2-3.11.2', 'title' => 'Scan for vulnerabilities in systems and applications', 'description' => 'Scan for vulnerabilities in organizational information systems and applications periodically and when new vulnerabilities affecting those systems and applications are identified.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'RA.L2-3.11.3', 'title' => 'Remediate vulnerabilities in accordance with assessments of risk', 'description' => 'Remediate vulnerabilities in accordance with assessments of risk.', 'ml_level' => 2],

        // Security Assessment (CA) domain
        ['framework' => 'CMMC', 'code' => 'CA.L2-3.12.1', 'title' => 'Assess security controls periodically', 'description' => 'Periodically assess the security controls in organizational information systems to determine if the controls are effective in their application.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'CA.L2-3.12.2', 'title' => 'Develop and implement plans of action', 'description' => 'Develop and implement plans of action designed to correct deficiencies and reduce or eliminate vulnerabilities in organizational information systems.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'CA.L2-3.12.3', 'title' => 'Monitor security controls on an ongoing basis', 'description' => 'Monitor security controls on an ongoing basis to ensure the continued effectiveness of the controls.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'CA.L2-3.12.4', 'title' => 'Develop, document, and periodically update system security plans', 'description' => 'Develop, document, and periodically update system security plans that describe system boundaries, system environments of operation, how security requirements are implemented, and the relationships with or connections to other systems.', 'ml_level' => 2],

        // System and Communications Protection (SC) domain
        ['framework' => 'CMMC', 'code' => 'SC.L2-3.13.2', 'title' => 'Implement security functions as a layered structure', 'description' => 'Implement subnetworks for publicly accessible system components that are physically or logically separated from internal networks.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'SC.L2-3.13.3', 'title' => 'Employ architectural designs and configurations', 'description' => 'Deny network communications traffic by default and allow network communications traffic by exception.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'SC.L2-3.13.4', 'title' => 'Prevent unauthorized information transfer', 'description' => 'Prevent unauthorized and unintended information transfer via shared system resources.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'SC.L2-3.13.6', 'title' => 'Deny communications by default and allow by exception', 'description' => 'Deny network communications traffic by default and allow network communications traffic by exception (i.e., deny all, permit by exception).', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'SC.L2-3.13.7', 'title' => 'Prevent remote devices from simultaneously establishing connections', 'description' => 'Prevent remote devices from simultaneously establishing non-remote connections with organizational information systems and communicating via some other connection to resources in external networks.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'SC.L2-3.13.8', 'title' => 'Implement cryptographic mechanisms for transmission', 'description' => 'Implement cryptographic mechanisms to prevent unauthorized disclosure of CUI during transmission unless otherwise protected by alternative physical safeguards.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'SC.L2-3.13.9', 'title' => 'Terminate network connections at end of sessions', 'description' => 'Terminate network connections associated with communications sessions at the end of the sessions or after a defined period of inactivity.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'SC.L2-3.13.10', 'title' => 'Establish and manage cryptographic keys', 'description' => 'Establish and manage cryptographic keys for cryptography employed in organizational information systems.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'SC.L2-3.13.11', 'title' => 'Employ FIPS-validated cryptography', 'description' => 'Employ FIPS-validated cryptography when used to protect the confidentiality of CUI.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'SC.L2-3.13.12', 'title' => 'Prohibit remote activation of collaborative computing devices', 'description' => 'Prohibit remote activation of collaborative computing devices and provide indication of devices in use to users present at the device.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'SC.L2-3.13.13', 'title' => 'Control and monitor use of mobile code', 'description' => 'Control and monitor the use of mobile code.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'SC.L2-3.13.14', 'title' => 'Control and monitor use of VoIP', 'description' => 'Control and monitor the use of Voice over Internet Protocol (VoIP) technologies.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'SC.L2-3.13.15', 'title' => 'Protect authenticity of communications sessions', 'description' => 'Protect the authenticity of communications sessions.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'SC.L2-3.13.16', 'title' => 'Protect confidentiality of CUI at rest', 'description' => 'Protect the confidentiality of CUI at rest.', 'ml_level' => 2],

        // System and Information Integrity (SI) domain
        ['framework' => 'CMMC', 'code' => 'SI.L2-3.14.3', 'title' => 'Monitor information system security alerts', 'description' => 'Monitor information system security alerts and advisories and take action in response.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'SI.L2-3.14.6', 'title' => 'Monitor organizational systems and communications', 'description' => 'Monitor organizational information systems, including inbound and outbound communications traffic, to detect attacks and indicators of potential attacks.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'SI.L2-3.14.7', 'title' => 'Identify unauthorized use of organizational systems', 'description' => 'Identify unauthorized use of organizational information systems.', 'ml_level' => 2],

        // Now add corresponding NIST 800-171 controls
        ['framework' => 'NIST800171', 'code' => '3.1.7', 'title' => 'Prevent non-privileged users from executing privileged functions', 'description' => 'Prevent non-privileged users from executing privileged functions and capture the execution of such functions in audit logs.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.1.8', 'title' => 'Limit unsuccessful logon attempts', 'description' => 'Limit unsuccessful logon attempts.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.1.9', 'title' => 'Provide privacy and security notices', 'description' => 'Provide privacy and security notices consistent with applicable CUI rules.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.1.10', 'title' => 'Use session lock with pattern-hiding displays', 'description' => 'Use session lock with pattern-hiding displays to prevent access/viewing of data after period of inactivity.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.1.11', 'title' => 'Terminate user session automatically', 'description' => 'Terminate (automatically) a user session after a defined condition.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.1.12', 'title' => 'Monitor and control remote access sessions', 'description' => 'Monitor and control remote access sessions.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.1.13', 'title' => 'Employ cryptographic mechanisms to protect confidentiality', 'description' => 'Employ cryptographic mechanisms to protect confidentiality of remote access sessions.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.1.14', 'title' => 'Route remote access via managed access control points', 'description' => 'Route remote access via managed access control points.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.1.15', 'title' => 'Authorize remote execution of privileged commands', 'description' => 'Authorize remote execution of privileged commands and remote access to security-relevant information.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.1.16', 'title' => 'Authorize wireless access prior to allowing such connections', 'description' => 'Authorize wireless access prior to allowing such connections.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.1.17', 'title' => 'Protect wireless access using authentication and encryption', 'description' => 'Protect wireless access using authentication and encryption.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.1.18', 'title' => 'Control connection of mobile devices', 'description' => 'Control connection of mobile devices.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.1.19', 'title' => 'Encrypt CUI on mobile devices and mobile computing platforms', 'description' => 'Encrypt CUI on mobile devices and mobile computing platforms.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.1.21', 'title' => 'Limit use of portable storage devices on external systems', 'description' => 'Limit use of organizational portable storage devices on external information systems.', 'ml_level' => null],

        ['framework' => 'NIST800171', 'code' => '3.4.7', 'title' => 'Restrict, disable, or prevent use of nonessential programs', 'description' => 'Restrict, disable, or prevent the use of nonessential programs, functions, ports, protocols, and services.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.4.8', 'title' => 'Apply deny-by-exception policy for software', 'description' => 'Apply deny-by-exception (blacklisting) policy to prevent the use of unauthorized software or deny-all, permit-by-exception (whitelisting) policy to allow the execution of authorized software.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.4.9', 'title' => 'Control and monitor user-installed software', 'description' => 'Control and monitor user-installed software.', 'ml_level' => null],

        ['framework' => 'NIST800171', 'code' => '3.5.3', 'title' => 'Use multifactor authentication', 'description' => 'Use multifactor authentication for local and network access to privileged accounts and for network access to non-privileged accounts.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.5.4', 'title' => 'Employ replay-resistant authentication mechanisms', 'description' => 'Employ replay-resistant authentication mechanisms for network access to privileged and non-privileged accounts.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.5.5', 'title' => 'Prevent reuse of identifiers for a defined period', 'description' => 'Prevent reuse of identifiers for a defined period.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.5.6', 'title' => 'Disable identifiers after a defined period of inactivity', 'description' => 'Disable identifiers after a defined period of inactivity.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.5.7', 'title' => 'Enforce minimum password complexity', 'description' => 'Enforce a minimum password complexity and change of characters when new passwords are created.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.5.8', 'title' => 'Prohibit password reuse', 'description' => 'Prohibit password reuse for a specified number of generations.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.5.9', 'title' => 'Allow temporary password use for system logons', 'description' => 'Allow temporary password use for system logons with an immediate change to a permanent password.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.5.10', 'title' => 'Store and transmit only cryptographically-protected passwords', 'description' => 'Store and transmit only cryptographically-protected passwords.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.5.11', 'title' => 'Obscure feedback of authentication information', 'description' => 'Obscure feedback of authentication information.', 'ml_level' => null],

        ['framework' => 'NIST800171', 'code' => '3.7.5', 'title' => 'Require multifactor authentication for remote maintenance', 'description' => 'Require multifactor authentication to establish nonlocal maintenance sessions via external network connections and terminate such connections when nonlocal maintenance is complete.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.7.6', 'title' => 'Supervise maintenance activities', 'description' => 'Supervise the maintenance activities of maintenance personnel without required access authorization.', 'ml_level' => null],

        ['framework' => 'NIST800171', 'code' => '3.8.4', 'title' => 'Mark media with necessary CUI markings', 'description' => 'Mark media with necessary CUI markings and distribution limitations.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.8.5', 'title' => 'Control access to media during transport', 'description' => 'Control access to media containing CUI and maintain accountability for media during transport outside of controlled areas.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.8.6', 'title' => 'Implement cryptographic mechanisms to protect CUI during transport', 'description' => 'Implement cryptographic mechanisms to protect the confidentiality of CUI stored on digital media during transport unless otherwise protected by alternative physical safeguards.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.8.7', 'title' => 'Control use of removable media', 'description' => 'Control the use of removable media on system components.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.8.8', 'title' => 'Prohibit use of portable storage devices without identifiable owner', 'description' => 'Prohibit the use of portable storage devices when such devices have no identifiable owner.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.8.9', 'title' => 'Protect confidentiality of backup CUI', 'description' => 'Protect the confidentiality of backup CUI at storage locations.', 'ml_level' => null],

        ['framework' => 'NIST800171', 'code' => '3.10.6', 'title' => 'Enforce safeguarding measures at alternate work sites', 'description' => 'Enforce safeguarding measures for CUI at alternate work sites.', 'ml_level' => null],

        ['framework' => 'NIST800171', 'code' => '3.13.9', 'title' => 'Terminate network connections at end of sessions', 'description' => 'Terminate network connections associated with communications sessions at the end of the sessions or after a defined period of inactivity.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.13.10', 'title' => 'Establish and manage cryptographic keys', 'description' => 'Establish and manage cryptographic keys for cryptography employed in organizational systems.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.13.11', 'title' => 'Employ FIPS-validated cryptography', 'description' => 'Employ FIPS-validated cryptography when used to protect the confidentiality of CUI.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.13.12', 'title' => 'Prohibit remote activation of collaborative computing devices', 'description' => 'Prohibit remote activation of collaborative computing devices and provide indication of devices in use to users present at the device.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.13.13', 'title' => 'Control and monitor use of mobile code', 'description' => 'Control and monitor the use of mobile code.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.13.14', 'title' => 'Control and monitor use of VoIP', 'description' => 'Control and monitor the use of Voice over Internet Protocol (VoIP) technologies.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.13.15', 'title' => 'Protect authenticity of communications sessions', 'description' => 'Protect the authenticity of communications sessions.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.13.16', 'title' => 'Protect confidentiality of CUI at rest', 'description' => 'Protect the confidentiality of CUI at rest.', 'ml_level' => null],

        ['framework' => 'NIST800171', 'code' => '3.14.3', 'title' => 'Monitor system security alerts and advisories', 'description' => 'Monitor system security alerts and advisories and take action in response.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.14.6', 'title' => 'Monitor systems for attacks and indicators of potential attacks', 'description' => 'Monitor organizational systems, including inbound and outbound communications traffic, to detect attacks and indicators of potential attacks.', 'ml_level' => null],
        ['framework' => 'NIST800171', 'code' => '3.14.7', 'title' => 'Identify unauthorized use of organizational systems', 'description' => 'Identify unauthorized use of organizational systems.', 'ml_level' => null],
    ];

    $inserted = 0;
    foreach ($controls as $control) {
        // Check if control already exists
        $existing = $db->fetchOne(
            'SELECT id FROM controls WHERE framework = ? AND code = ?',
            [$control['framework'], $control['code']]
        );

        if (!$existing) {
            $db->insert('controls', $control);
            $inserted++;
        }
    }

    // Return count for logging
    return $inserted;
};
