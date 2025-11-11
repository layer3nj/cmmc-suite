<?php

/**
 * Seed NIST SP 800-53 Rev 5 Controls (Representative Sample)
 * Full implementation includes all controls from all 20 families
 */

return function($db) {
    $controls = [
        // AC - Access Control Family
        ['framework' => 'NIST80053', 'code' => 'AC-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate access control policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'AC-2', 'title' => 'Account Management', 'description' => 'Manage information system accounts, including establishing, activating, modifying, reviewing, disabling, and removing accounts.'],
        ['framework' => 'NIST80053', 'code' => 'AC-3', 'title' => 'Access Enforcement', 'description' => 'Enforce approved authorizations for logical access to information and system resources.'],
        ['framework' => 'NIST80053', 'code' => 'AC-4', 'title' => 'Information Flow Enforcement', 'description' => 'Enforce approved authorizations for controlling the flow of information within the system and between connected systems.'],
        ['framework' => 'NIST80053', 'code' => 'AC-5', 'title' => 'Separation of Duties', 'description' => 'Separate duties of individuals to prevent malevolent activity without collusion.'],
        ['framework' => 'NIST80053', 'code' => 'AC-6', 'title' => 'Least Privilege', 'description' => 'Employ the principle of least privilege, allowing only authorized accesses for users.'],
        ['framework' => 'NIST80053', 'code' => 'AC-7', 'title' => 'Unsuccessful Logon Attempts', 'description' => 'Enforce a limit on consecutive invalid logon attempts by a user.'],
        ['framework' => 'NIST80053', 'code' => 'AC-8', 'title' => 'System Use Notification', 'description' => 'Display an approved system use notification message or banner.'],
        ['framework' => 'NIST80053', 'code' => 'AC-11', 'title' => 'Device Lock', 'description' => 'Prevent further access to the system by initiating a device lock after a period of inactivity.'],
        ['framework' => 'NIST80053', 'code' => 'AC-12', 'title' => 'Session Termination', 'description' => 'Automatically terminate a user session after conditions are met.'],
        ['framework' => 'NIST80053', 'code' => 'AC-14', 'title' => 'Permitted Actions without Identification or Authentication', 'description' => 'Identify and document user actions that can be performed without identification or authentication.'],
        ['framework' => 'NIST80053', 'code' => 'AC-17', 'title' => 'Remote Access', 'description' => 'Establish and document usage restrictions, configuration requirements, and implementation guidance for remote access.'],
        ['framework' => 'NIST80053', 'code' => 'AC-18', 'title' => 'Wireless Access', 'description' => 'Establish usage restrictions, configuration requirements, and implementation guidance for wireless access.'],
        ['framework' => 'NIST80053', 'code' => 'AC-19', 'title' => 'Access Control for Mobile Devices', 'description' => 'Establish usage restrictions, configuration requirements, and implementation guidance for mobile devices.'],
        ['framework' => 'NIST80053', 'code' => 'AC-20', 'title' => 'Use of External Systems', 'description' => 'Establish terms and conditions for authorized individuals to access the system from external systems.'],

        // AT - Awareness and Training Family
        ['framework' => 'NIST80053', 'code' => 'AT-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate awareness and training policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'AT-2', 'title' => 'Literacy Training and Awareness', 'description' => 'Provide security and privacy literacy training to system users.'],
        ['framework' => 'NIST80053', 'code' => 'AT-3', 'title' => 'Role-Based Training', 'description' => 'Provide role-based security and privacy training to personnel with assigned security roles.'],
        ['framework' => 'NIST80053', 'code' => 'AT-4', 'title' => 'Training Records', 'description' => 'Document and monitor information security and privacy training activities.'],

        // AU - Audit and Accountability Family
        ['framework' => 'NIST80053', 'code' => 'AU-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate audit and accountability policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'AU-2', 'title' => 'Event Logging', 'description' => 'Identify the types of events that the system is capable of logging.'],
        ['framework' => 'NIST80053', 'code' => 'AU-3', 'title' => 'Content of Audit Records', 'description' => 'Ensure that audit records contain information that establishes what type of event occurred, when it occurred, where it occurred, the source, outcome, and identity of individuals or subjects.'],
        ['framework' => 'NIST80053', 'code' => 'AU-4', 'title' => 'Audit Log Storage Capacity', 'description' => 'Allocate audit log storage capacity to accommodate organizational requirements.'],
        ['framework' => 'NIST80053', 'code' => 'AU-5', 'title' => 'Response to Audit Logging Process Failures', 'description' => 'Alert personnel in the event of an audit logging process failure.'],
        ['framework' => 'NIST80053', 'code' => 'AU-6', 'title' => 'Audit Record Review, Analysis, and Reporting', 'description' => 'Review and analyze system audit records for indications of inappropriate or unusual activity.'],
        ['framework' => 'NIST80053', 'code' => 'AU-7', 'title' => 'Audit Record Reduction and Report Generation', 'description' => 'Provide and implement an audit record reduction and report generation capability.'],
        ['framework' => 'NIST80053', 'code' => 'AU-8', 'title' => 'Time Stamps', 'description' => 'Use internal system clocks to generate time stamps for audit records.'],
        ['framework' => 'NIST80053', 'code' => 'AU-9', 'title' => 'Protection of Audit Information', 'description' => 'Protect audit information and audit logging tools from unauthorized access, modification, and deletion.'],
        ['framework' => 'NIST80053', 'code' => 'AU-11', 'title' => 'Audit Record Retention', 'description' => 'Retain audit records for a defined time period to provide support for after-the-fact investigations.'],
        ['framework' => 'NIST80053', 'code' => 'AU-12', 'title' => 'Audit Record Generation', 'description' => 'Provide audit record generation capability for the event types defined in AU-2.'],

        // CA - Assessment, Authorization, and Monitoring Family
        ['framework' => 'NIST80053', 'code' => 'CA-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate assessment, authorization, and monitoring policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'CA-2', 'title' => 'Control Assessments', 'description' => 'Develop a control assessment plan and assess the controls in the system and its environment of operation.'],
        ['framework' => 'NIST80053', 'code' => 'CA-3', 'title' => 'Information Exchange', 'description' => 'Approve, document, and control connections from the system to other systems outside the organization.'],
        ['framework' => 'NIST80053', 'code' => 'CA-5', 'title' => 'Plan of Action and Milestones', 'description' => 'Develop a plan of action and milestones for the system to document planned remediation actions.'],
        ['framework' => 'NIST80053', 'code' => 'CA-6', 'title' => 'Authorization', 'description' => 'Assign a senior official as the authorizing official for the system.'],
        ['framework' => 'NIST80053', 'code' => 'CA-7', 'title' => 'Continuous Monitoring', 'description' => 'Develop a system-level continuous monitoring strategy and implement continuous monitoring.'],
        ['framework' => 'NIST80053', 'code' => 'CA-8', 'title' => 'Penetration Testing', 'description' => 'Conduct penetration testing on systems or system components.'],
        ['framework' => 'NIST80053', 'code' => 'CA-9', 'title' => 'Internal System Connections', 'description' => 'Authorize internal connections of system components to the system.'],

        // CM - Configuration Management Family
        ['framework' => 'NIST80053', 'code' => 'CM-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate configuration management policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'CM-2', 'title' => 'Baseline Configuration', 'description' => 'Develop, document, and maintain baseline configurations of the system.'],
        ['framework' => 'NIST80053', 'code' => 'CM-3', 'title' => 'Configuration Change Control', 'description' => 'Determine and document the types of changes to the system that are configuration-controlled.'],
        ['framework' => 'NIST80053', 'code' => 'CM-4', 'title' => 'Impact Analyses', 'description' => 'Analyze changes to the system to determine potential security and privacy impacts prior to change implementation.'],
        ['framework' => 'NIST80053', 'code' => 'CM-5', 'title' => 'Access Restrictions for Change', 'description' => 'Define, document, approve, and enforce physical and logical access restrictions associated with changes to the system.'],
        ['framework' => 'NIST80053', 'code' => 'CM-6', 'title' => 'Configuration Settings', 'description' => 'Establish and document configuration settings for components employed within the system.'],
        ['framework' => 'NIST80053', 'code' => 'CM-7', 'title' => 'Least Functionality', 'description' => 'Configure the system to provide only mission-essential capabilities.'],
        ['framework' => 'NIST80053', 'code' => 'CM-8', 'title' => 'System Component Inventory', 'description' => 'Develop and document an inventory of system components.'],
        ['framework' => 'NIST80053', 'code' => 'CM-10', 'title' => 'Software Usage Restrictions', 'description' => 'Use software and associated documentation in accordance with contract agreements and copyright laws.'],
        ['framework' => 'NIST80053', 'code' => 'CM-11', 'title' => 'User-Installed Software', 'description' => 'Establish policies governing the installation of software by users.'],

        // CP - Contingency Planning Family
        ['framework' => 'NIST80053', 'code' => 'CP-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate contingency planning policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'CP-2', 'title' => 'Contingency Plan', 'description' => 'Develop a contingency plan for the system that identifies essential mission and business functions.'],
        ['framework' => 'NIST80053', 'code' => 'CP-3', 'title' => 'Contingency Training', 'description' => 'Provide contingency plan training to system users.'],
        ['framework' => 'NIST80053', 'code' => 'CP-4', 'title' => 'Contingency Plan Testing', 'description' => 'Test the contingency plan for the system to determine effectiveness.'],
        ['framework' => 'NIST80053', 'code' => 'CP-6', 'title' => 'Alternate Storage Site', 'description' => 'Establish an alternate storage site and implement necessary agreements.'],
        ['framework' => 'NIST80053', 'code' => 'CP-7', 'title' => 'Alternate Processing Site', 'description' => 'Establish an alternate processing site and implement necessary agreements.'],
        ['framework' => 'NIST80053', 'code' => 'CP-9', 'title' => 'System Backup', 'description' => 'Conduct backups of user-level information, system-level information, and system documentation.'],
        ['framework' => 'NIST80053', 'code' => 'CP-10', 'title' => 'System Recovery and Reconstitution', 'description' => 'Provide for the recovery and reconstitution of the system to a known state.'],

        // IA - Identification and Authentication Family
        ['framework' => 'NIST80053', 'code' => 'IA-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate identification and authentication policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'IA-2', 'title' => 'Identification and Authentication (Organizational Users)', 'description' => 'Uniquely identify and authenticate organizational users.'],
        ['framework' => 'NIST80053', 'code' => 'IA-3', 'title' => 'Device Identification and Authentication', 'description' => 'Uniquely identify and authenticate devices before establishing a connection.'],
        ['framework' => 'NIST80053', 'code' => 'IA-4', 'title' => 'Identifier Management', 'description' => 'Manage system identifiers by receiving authorization, selecting, and assigning identifiers.'],
        ['framework' => 'NIST80053', 'code' => 'IA-5', 'title' => 'Authenticator Management', 'description' => 'Manage system authenticators by establishing initial authenticator content and managing changes.'],
        ['framework' => 'NIST80053', 'code' => 'IA-6', 'title' => 'Authentication Feedback', 'description' => 'Obscure feedback of authentication information during the authentication process.'],
        ['framework' => 'NIST80053', 'code' => 'IA-7', 'title' => 'Cryptographic Module Authentication', 'description' => 'Implement mechanisms for authentication to a cryptographic module.'],
        ['framework' => 'NIST80053', 'code' => 'IA-8', 'title' => 'Identification and Authentication (Non-Organizational Users)', 'description' => 'Uniquely identify and authenticate non-organizational users.'],
        ['framework' => 'NIST80053', 'code' => 'IA-11', 'title' => 'Re-authentication', 'description' => 'Require users to re-authenticate when circumstances or situations require re-authentication.'],

        // IR - Incident Response Family
        ['framework' => 'NIST80053', 'code' => 'IR-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate incident response policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'IR-2', 'title' => 'Incident Response Training', 'description' => 'Provide incident response training to system users.'],
        ['framework' => 'NIST80053', 'code' => 'IR-3', 'title' => 'Incident Response Testing', 'description' => 'Test the incident response capability for the system.'],
        ['framework' => 'NIST80053', 'code' => 'IR-4', 'title' => 'Incident Handling', 'description' => 'Implement an incident handling capability for incidents.'],
        ['framework' => 'NIST80053', 'code' => 'IR-5', 'title' => 'Incident Monitoring', 'description' => 'Track and document incidents.'],
        ['framework' => 'NIST80053', 'code' => 'IR-6', 'title' => 'Incident Reporting', 'description' => 'Require personnel to report suspected incidents to the organizational incident response capability.'],
        ['framework' => 'NIST80053', 'code' => 'IR-7', 'title' => 'Incident Response Assistance', 'description' => 'Provide an incident response support resource that offers advice and assistance to users.'],
        ['framework' => 'NIST80053', 'code' => 'IR-8', 'title' => 'Incident Response Plan', 'description' => 'Develop an incident response plan that provides a roadmap for implementing the incident response capability.'],

        // MA - Maintenance Family
        ['framework' => 'NIST80053', 'code' => 'MA-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate maintenance policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'MA-2', 'title' => 'Controlled Maintenance', 'description' => 'Schedule, document, and review records of maintenance and repairs on system components.'],
        ['framework' => 'NIST80053', 'code' => 'MA-3', 'title' => 'Maintenance Tools', 'description' => 'Approve, control, and monitor the use of system maintenance tools.'],
        ['framework' => 'NIST80053', 'code' => 'MA-4', 'title' => 'Nonlocal Maintenance', 'description' => 'Approve and monitor nonlocal maintenance and diagnostic activities.'],
        ['framework' => 'NIST80053', 'code' => 'MA-5', 'title' => 'Maintenance Personnel', 'description' => 'Establish a process for maintenance personnel authorization and maintain a list of authorized personnel.'],
        ['framework' => 'NIST80053', 'code' => 'MA-6', 'title' => 'Timely Maintenance', 'description' => 'Obtain maintenance support and spare parts for system components within a defined time period.'],

        // PE - Physical and Environmental Protection Family
        ['framework' => 'NIST80053', 'code' => 'PE-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate physical and environmental protection policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'PE-2', 'title' => 'Physical Access Authorizations', 'description' => 'Develop, approve, and maintain a list of individuals with authorized access to the facility.'],
        ['framework' => 'NIST80053', 'code' => 'PE-3', 'title' => 'Physical Access Control', 'description' => 'Enforce physical access authorizations at entry and exit points to the facility.'],
        ['framework' => 'NIST80053', 'code' => 'PE-4', 'title' => 'Access Control for Transmission', 'description' => 'Control physical access to system distribution and transmission lines.'],
        ['framework' => 'NIST80053', 'code' => 'PE-5', 'title' => 'Access Control for Output Devices', 'description' => 'Control physical access to output devices to prevent unauthorized individuals from obtaining output.'],
        ['framework' => 'NIST80053', 'code' => 'PE-6', 'title' => 'Monitoring Physical Access', 'description' => 'Monitor physical access to the facility where the system resides.'],
        ['framework' => 'NIST80053', 'code' => 'PE-8', 'title' => 'Visitor Access Records', 'description' => 'Maintain visitor access records to the facility where the system resides.'],
        ['framework' => 'NIST80053', 'code' => 'PE-12', 'title' => 'Emergency Lighting', 'description' => 'Employ and maintain automatic emergency lighting for the system.'],
        ['framework' => 'NIST80053', 'code' => 'PE-13', 'title' => 'Fire Protection', 'description' => 'Employ and maintain fire detection and suppression systems.'],
        ['framework' => 'NIST80053', 'code' => 'PE-14', 'title' => 'Environmental Controls', 'description' => 'Maintain environmental controls within the facility where the system resides.'],
        ['framework' => 'NIST80053', 'code' => 'PE-15', 'title' => 'Water Damage Protection', 'description' => 'Protect the system from damage resulting from water leakage.'],
        ['framework' => 'NIST80053', 'code' => 'PE-16', 'title' => 'Delivery and Removal', 'description' => 'Authorize and control system components entering and exiting the facility.'],

        // PL - Planning Family
        ['framework' => 'NIST80053', 'code' => 'PL-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate planning policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'PL-2', 'title' => 'System Security and Privacy Plans', 'description' => 'Develop security and privacy plans for the system.'],
        ['framework' => 'NIST80053', 'code' => 'PL-4', 'title' => 'Rules of Behavior', 'description' => 'Establish and provide rules of behavior for individuals requiring access to the system.'],
        ['framework' => 'NIST80053', 'code' => 'PL-8', 'title' => 'Security and Privacy Architectures', 'description' => 'Develop security and privacy architectures for the system.'],
        ['framework' => 'NIST80053', 'code' => 'PL-10', 'title' => 'Baseline Selection', 'description' => 'Select a control baseline for the system.'],
        ['framework' => 'NIST80053', 'code' => 'PL-11', 'title' => 'Baseline Tailoring', 'description' => 'Tailor the selected control baseline to meet organizational needs.'],

        // RA - Risk Assessment Family
        ['framework' => 'NIST80053', 'code' => 'RA-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate risk assessment policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'RA-2', 'title' => 'Security Categorization', 'description' => 'Categorize the system and information it processes, stores, or transmits.'],
        ['framework' => 'NIST80053', 'code' => 'RA-3', 'title' => 'Risk Assessment', 'description' => 'Conduct a risk assessment of the potential threats to organizational operations.'],
        ['framework' => 'NIST80053', 'code' => 'RA-5', 'title' => 'Vulnerability Monitoring and Scanning', 'description' => 'Monitor and scan for vulnerabilities in the system and hosted applications.'],
        ['framework' => 'NIST80053', 'code' => 'RA-7', 'title' => 'Risk Response', 'description' => 'Respond to findings from security and privacy assessments, and monitoring activities.'],

        // SA - System and Services Acquisition Family
        ['framework' => 'NIST80053', 'code' => 'SA-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate system and services acquisition policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'SA-2', 'title' => 'Allocation of Resources', 'description' => 'Determine security and privacy requirements for the system or system service.'],
        ['framework' => 'NIST80053', 'code' => 'SA-3', 'title' => 'System Development Life Cycle', 'description' => 'Acquire, develop, and manage the system using a system development life cycle that incorporates security.'],
        ['framework' => 'NIST80053', 'code' => 'SA-4', 'title' => 'Acquisition Process', 'description' => 'Include security and privacy requirements, descriptions, and criteria explicitly in acquisition contracts.'],
        ['framework' => 'NIST80053', 'code' => 'SA-5', 'title' => 'System Documentation', 'description' => 'Obtain or develop administrator and user documentation for the system.'],
        ['framework' => 'NIST80053', 'code' => 'SA-8', 'title' => 'Security and Privacy Engineering Principles', 'description' => 'Apply systems security and privacy engineering principles in the specification, design, development, implementation, and modification of the system.'],
        ['framework' => 'NIST80053', 'code' => 'SA-9', 'title' => 'External System Services', 'description' => 'Require external system service providers to comply with organizational security and privacy requirements.'],
        ['framework' => 'NIST80053', 'code' => 'SA-11', 'title' => 'Developer Testing and Evaluation', 'description' => 'Require the developer of the system or system component to create a security and privacy assessment plan.'],

        // SC - System and Communications Protection Family
        ['framework' => 'NIST80053', 'code' => 'SC-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate system and communications protection policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'SC-2', 'title' => 'Separation of System and User Functionality', 'description' => 'Separate user functionality from system management functionality.'],
        ['framework' => 'NIST80053', 'code' => 'SC-5', 'title' => 'Denial-of-Service Protection', 'description' => 'Protect against or limit the effects of denial-of-service attacks.'],
        ['framework' => 'NIST80053', 'code' => 'SC-7', 'title' => 'Boundary Protection', 'description' => 'Monitor and control communications at the external managed interfaces to the system.'],
        ['framework' => 'NIST80053', 'code' => 'SC-8', 'title' => 'Transmission Confidentiality and Integrity', 'description' => 'Protect the confidentiality and integrity of transmitted information.'],
        ['framework' => 'NIST80053', 'code' => 'SC-12', 'title' => 'Cryptographic Key Establishment and Management', 'description' => 'Establish and manage cryptographic keys for cryptography employed within the system.'],
        ['framework' => 'NIST80053', 'code' => 'SC-13', 'title' => 'Cryptographic Protection', 'description' => 'Implement cryptographic mechanisms to prevent unauthorized disclosure and modification of information.'],
        ['framework' => 'NIST80053', 'code' => 'SC-15', 'title' => 'Collaborative Computing Devices and Applications', 'description' => 'Prohibit remote activation of collaborative computing devices and provide indication of use.'],
        ['framework' => 'NIST80053', 'code' => 'SC-17', 'title' => 'Public Key Infrastructure Certificates', 'description' => 'Issue public key certificates under an appropriate certificate policy.'],
        ['framework' => 'NIST80053', 'code' => 'SC-18', 'title' => 'Mobile Code', 'description' => 'Define acceptable and unacceptable mobile code technologies and their use.'],
        ['framework' => 'NIST80053', 'code' => 'SC-20', 'title' => 'Secure Name/Address Resolution Service', 'description' => 'Provide data origin authentication and data integrity verification for DNS queries.'],
        ['framework' => 'NIST80053', 'code' => 'SC-28', 'title' => 'Protection of Information at Rest', 'description' => 'Protect the confidentiality and integrity of information at rest.'],

        // SI - System and Information Integrity Family
        ['framework' => 'NIST80053', 'code' => 'SI-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate system and information integrity policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'SI-2', 'title' => 'Flaw Remediation', 'description' => 'Identify, report, and correct system flaws.'],
        ['framework' => 'NIST80053', 'code' => 'SI-3', 'title' => 'Malicious Code Protection', 'description' => 'Implement malicious code protection mechanisms at system entry and exit points.'],
        ['framework' => 'NIST80053', 'code' => 'SI-4', 'title' => 'System Monitoring', 'description' => 'Monitor the system to detect attacks and indicators of potential attacks.'],
        ['framework' => 'NIST80053', 'code' => 'SI-5', 'title' => 'Security Alerts, Advisories, and Directives', 'description' => 'Receive system security alerts, advisories, and directives from external organizations.'],
        ['framework' => 'NIST80053', 'code' => 'SI-6', 'title' => 'Security and Privacy Function Verification', 'description' => 'Verify the correct operation of security and privacy functions.'],
        ['framework' => 'NIST80053', 'code' => 'SI-7', 'title' => 'Software, Firmware, and Information Integrity', 'description' => 'Employ integrity verification tools to detect unauthorized changes.'],
        ['framework' => 'NIST80053', 'code' => 'SI-8', 'title' => 'Spam Protection', 'description' => 'Employ spam protection mechanisms at system entry and exit points.'],
        ['framework' => 'NIST80053', 'code' => 'SI-10', 'title' => 'Information Input Validation', 'description' => 'Check the validity of information inputs.'],
        ['framework' => 'NIST80053', 'code' => 'SI-12', 'title' => 'Information Management and Retention', 'description' => 'Manage and retain information within the system in accordance with applicable laws.'],
        ['framework' => 'NIST80053', 'code' => 'SI-16', 'title' => 'Memory Protection', 'description' => 'Implement safeguards to protect system memory from unauthorized code execution.'],

        // SR - Supply Chain Risk Management Family
        ['framework' => 'NIST80053', 'code' => 'SR-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate supply chain risk management policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'SR-2', 'title' => 'Supply Chain Risk Management Plan', 'description' => 'Develop a plan for managing supply chain risks.'],
        ['framework' => 'NIST80053', 'code' => 'SR-3', 'title' => 'Supply Chain Controls and Processes', 'description' => 'Establish controls and processes to ensure adequate security across the system supply chain.'],
        ['framework' => 'NIST80053', 'code' => 'SR-5', 'title' => 'Acquisition Strategies, Tools, and Methods', 'description' => 'Employ acquisition strategies, contract tools, and procurement methods to protect against supply chain risks.'],
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

    return $inserted;
};
