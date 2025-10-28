<?php

/**
 * Add additional compliance frameworks:
 * - HIPAA Security Rule
 * - FTC Safeguards Rule
 * - PCI-DSS v4.0
 * - SOC 2
 * - ISO 27001:2022
 */

return function($db) {
    $controls = [
        // ===== HIPAA SECURITY RULE =====
        // Administrative Safeguards (45 CFR § 164.308)
        ['framework' => 'HIPAA', 'code' => '164.308(a)(1)(i)', 'title' => 'Security Management Process', 'description' => 'Implement policies and procedures to prevent, detect, contain, and correct security violations.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(1)(ii)(A)', 'title' => 'Risk Analysis (Required)', 'description' => 'Conduct an accurate and thorough assessment of the potential risks and vulnerabilities to the confidentiality, integrity, and availability of electronic protected health information held by the covered entity or business associate.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(1)(ii)(B)', 'title' => 'Risk Management (Required)', 'description' => 'Implement security measures sufficient to reduce risks and vulnerabilities to a reasonable and appropriate level.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(1)(ii)(C)', 'title' => 'Sanction Policy (Required)', 'description' => 'Apply appropriate sanctions against workforce members who fail to comply with security policies and procedures.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(1)(ii)(D)', 'title' => 'Information System Activity Review (Required)', 'description' => 'Implement procedures to regularly review records of information system activity such as audit logs, access reports, and security incident tracking reports.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(2)', 'title' => 'Assigned Security Responsibility (Required)', 'description' => 'Identify the security official who is responsible for the development and implementation of the policies and procedures required by this subpart.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(3)(i)', 'title' => 'Workforce Security', 'description' => 'Implement policies and procedures to ensure that all members of its workforce have appropriate access to electronic protected health information.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(3)(ii)(A)', 'title' => 'Authorization and/or Supervision (Addressable)', 'description' => 'Implement procedures for the authorization and/or supervision of workforce members who work with ePHI or in locations where it might be accessed.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(3)(ii)(B)', 'title' => 'Workforce Clearance Procedure (Addressable)', 'description' => 'Implement procedures to determine that the access of a workforce member to ePHI is appropriate.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(3)(ii)(C)', 'title' => 'Termination Procedures (Addressable)', 'description' => 'Implement procedures for terminating access to ePHI when the employment of, or other arrangement with, a workforce member ends.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(4)(i)', 'title' => 'Information Access Management', 'description' => 'Implement policies and procedures for authorizing access to ePHI that are consistent with applicable HIPAA Privacy Rule provisions.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(5)(i)', 'title' => 'Security Awareness and Training', 'description' => 'Implement a security awareness and training program for all members of its workforce.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(6)(i)', 'title' => 'Security Incident Procedures (Required)', 'description' => 'Implement policies and procedures to address security incidents.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(7)(i)', 'title' => 'Contingency Plan (Required)', 'description' => 'Establish (and implement as needed) policies and procedures for responding to an emergency or other occurrence that damages systems containing ePHI.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(7)(ii)(A)', 'title' => 'Data Backup Plan (Required)', 'description' => 'Establish and implement procedures to create and maintain retrievable exact copies of ePHI.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(7)(ii)(B)', 'title' => 'Disaster Recovery Plan (Required)', 'description' => 'Establish (and implement as needed) procedures to restore any loss of data.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(7)(ii)(C)', 'title' => 'Emergency Mode Operation Plan (Required)', 'description' => 'Establish (and implement as needed) procedures to enable continuation of critical business processes for protection of ePHI while operating in emergency mode.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(8)', 'title' => 'Evaluation (Required)', 'description' => 'Perform a periodic technical and nontechnical evaluation that establishes the extent to which security policies and procedures meet security requirements.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.308(b)(1)', 'title' => 'Business Associate Contracts (Required)', 'description' => 'A covered entity may permit a business associate to create, receive, maintain, or transmit ePHI on the covered entity's behalf only if the covered entity obtains satisfactory assurances.', 'ml_level' => null],

        // Physical Safeguards (45 CFR § 164.310)
        ['framework' => 'HIPAA', 'code' => '164.310(a)(1)', 'title' => 'Facility Access Controls', 'description' => 'Implement policies and procedures to limit physical access to its electronic information systems and the facility or facilities in which they are housed.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.310(a)(2)(i)', 'title' => 'Contingency Operations (Addressable)', 'description' => 'Establish (and implement as needed) procedures that allow facility access in support of restoration of lost data under the disaster recovery plan and emergency mode operations plan.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.310(a)(2)(ii)', 'title' => 'Facility Security Plan (Addressable)', 'description' => 'Implement policies and procedures to safeguard the facility and the equipment therein from unauthorized physical access, tampering, and theft.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.310(a)(2)(iii)', 'title' => 'Access Control and Validation Procedures (Addressable)', 'description' => 'Implement procedures to control and validate a person\'s access to facilities based on their role or function.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.310(a)(2)(iv)', 'title' => 'Maintenance Records (Addressable)', 'description' => 'Implement policies and procedures to document repairs and modifications to the physical components of a facility.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.310(b)', 'title' => 'Workstation Use (Required)', 'description' => 'Implement policies and procedures that specify the proper functions to be performed and the manner in which those functions are to be performed on a workstation.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.310(c)', 'title' => 'Workstation Security (Required)', 'description' => 'Implement physical safeguards for all workstations that access ePHI to restrict access to authorized users.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.310(d)(1)', 'title' => 'Device and Media Controls', 'description' => 'Implement policies and procedures that govern the receipt and removal of hardware and electronic media that contain ePHI.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.310(d)(2)(i)', 'title' => 'Disposal (Required)', 'description' => 'Implement policies and procedures to address the final disposition of ePHI and/or the hardware or electronic media on which it is stored.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.310(d)(2)(ii)', 'title' => 'Media Re-use (Required)', 'description' => 'Implement procedures for removal of ePHI from electronic media before the media are made available for re-use.', 'ml_level' => null],

        // Technical Safeguards (45 CFR § 164.312)
        ['framework' => 'HIPAA', 'code' => '164.312(a)(1)', 'title' => 'Access Control (Required)', 'description' => 'Implement technical policies and procedures for electronic information systems that maintain ePHI to allow access only to those persons or software programs that have been granted access rights.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.312(a)(2)(i)', 'title' => 'Unique User Identification (Required)', 'description' => 'Assign a unique name and/or number for identifying and tracking user identity.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.312(a)(2)(ii)', 'title' => 'Emergency Access Procedure (Required)', 'description' => 'Establish (and implement as needed) procedures for obtaining necessary ePHI during an emergency.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.312(a)(2)(iii)', 'title' => 'Automatic Logoff (Addressable)', 'description' => 'Implement electronic procedures that terminate an electronic session after a predetermined time of inactivity.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.312(a)(2)(iv)', 'title' => 'Encryption and Decryption (Addressable)', 'description' => 'Implement a mechanism to encrypt and decrypt ePHI.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.312(b)', 'title' => 'Audit Controls (Required)', 'description' => 'Implement hardware, software, and/or procedural mechanisms that record and examine activity in information systems that contain or use ePHI.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.312(c)(1)', 'title' => 'Integrity', 'description' => 'Implement policies and procedures to protect ePHI from improper alteration or destruction.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.312(c)(2)', 'title' => 'Mechanism to Authenticate ePHI (Addressable)', 'description' => 'Implement electronic mechanisms to corroborate that ePHI has not been altered or destroyed in an unauthorized manner.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.312(d)', 'title' => 'Person or Entity Authentication (Required)', 'description' => 'Implement procedures to verify that a person or entity seeking access to ePHI is the one claimed.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.312(e)(1)', 'title' => 'Transmission Security', 'description' => 'Implement technical security measures to guard against unauthorized access to ePHI that is being transmitted over an electronic communications network.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.312(e)(2)(i)', 'title' => 'Integrity Controls (Addressable)', 'description' => 'Implement security measures to ensure that electronically transmitted ePHI is not improperly modified without detection.', 'ml_level' => null],
        ['framework' => 'HIPAA', 'code' => '164.312(e)(2)(ii)', 'title' => 'Encryption (Addressable)', 'description' => 'Implement a mechanism to encrypt ePHI whenever deemed appropriate.', 'ml_level' => null],

        // ===== FTC SAFEGUARDS RULE =====
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.3(a)', 'title' => 'Board Approval', 'description' => 'A financial institution must report to its board of directors or equivalent governing body at regular intervals, and no less frequently than annually.', 'ml_level' => null],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(a)', 'title' => 'Designate Qualified Individual', 'description' => 'Designate a qualified individual to implement and supervise the institution\'s information security program.', 'ml_level' => null],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(b)', 'title' => 'Risk Assessment', 'description' => 'Conduct a risk assessment to identify reasonably foreseeable internal and external risks to the security, confidentiality, and integrity of customer information.', 'ml_level' => null],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(1)', 'title' => 'Access Controls', 'description' => 'Control physical and logical access to information systems and customer information only to authorized persons.', 'ml_level' => null],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(2)', 'title' => 'Encryption of Customer Information', 'description' => 'Encrypt customer information in transit and at rest.', 'ml_level' => null],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(3)', 'title' => 'Secure Development Practices', 'description' => 'Develop, implement, and maintain secure development practices and procedures for in-house applications.', 'ml_level' => null],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(4)', 'title' => 'Multi-Factor Authentication', 'description' => 'Implement multi-factor authentication for any individual accessing customer information.', 'ml_level' => null],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(5)', 'title' => 'Asset Inventory', 'description' => 'Maintain an inventory of systems, applications, and infrastructure authorized to store, process, or transmit customer information.', 'ml_level' => null],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(6)', 'title' => 'Change Management', 'description' => 'Implement procedures to evaluate and approve information systems changes that may affect the security of customer information.', 'ml_level' => null],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(c)(7)', 'title' => 'Log Retention', 'description' => 'Maintain audit logs of access to customer information and monitor the effectiveness of key controls.', 'ml_level' => null],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(d)', 'title' => 'Penetration Testing and Vulnerability Assessment', 'description' => 'Conduct periodic vulnerability assessments and penetration testing.', 'ml_level' => null],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(e)', 'title' => 'Security Awareness Training', 'description' => 'Train personnel to implement the institution\'s information security program.', 'ml_level' => null],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(f)', 'title' => 'Service Provider Oversight', 'description' => 'Take reasonable steps to select and retain service providers capable of maintaining appropriate safeguards.', 'ml_level' => null],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(g)', 'title' => 'Incident Response Plan', 'description' => 'Implement an incident response plan to promptly respond to security events.', 'ml_level' => null],
        ['framework' => 'FTC-SAFEGUARDS', 'code' => '314.4(h)', 'title' => 'Monitoring and Testing', 'description' => 'Regularly test the key controls, systems, and procedures of the information security program.', 'ml_level' => null],

        // ===== PCI-DSS v4.0 (Sample - Key Requirements) =====
        ['framework' => 'PCI-DSS', 'code' => '1.1.1', 'title' => 'Network Security Controls', 'description' => 'Processes and mechanisms for establishing and maintaining network security controls are defined and understood.', 'ml_level' => null],
        ['framework' => 'PCI-DSS', 'code' => '1.2.1', 'title' => 'Configuration Standards for NSCs', 'description' => 'Configuration standards for NSC rulesets are defined and implemented.', 'ml_level' => null],
        ['framework' => 'PCI-DSS', 'code' => '2.1.1', 'title' => 'Default Passwords Changed', 'description' => 'All vendor-supplied default passwords are changed before systems are put into production.', 'ml_level' => null],
        ['framework' => 'PCI-DSS', 'code' => '3.2.1', 'title' => 'Stored Cardholder Data Minimized', 'description' => 'Account data storage is kept to a minimum.', 'ml_level' => null],
        ['framework' => 'PCI-DSS', 'code' => '3.3.1', 'title' => 'SAD Not Retained After Authorization', 'description' => 'Sensitive authentication data (SAD) is not retained after authorization.', 'ml_level' => null],
        ['framework' => 'PCI-DSS', 'code' => '3.5.1', 'title' => 'PAN Rendered Unreadable', 'description' => 'Primary account number (PAN) is rendered unreadable anywhere it is stored.', 'ml_level' => null],
        ['framework' => 'PCI-DSS', 'code' => '4.2.1', 'title' => 'Strong Cryptography for PAN Transmission', 'description' => 'Strong cryptography and security protocols are implemented to safeguard PAN during transmission.', 'ml_level' => null],
        ['framework' => 'PCI-DSS', 'code' => '5.2.1', 'title' => 'Anti-Malware Solutions Deployed', 'description' => 'Anti-malware solutions are deployed on all system components affected by malware.', 'ml_level' => null],
        ['framework' => 'PCI-DSS', 'code' => '6.2.3', 'title' => 'Security Vulnerabilities Addressed', 'description' => 'All system components are protected from known vulnerabilities by installing applicable security patches/updates.', 'ml_level' => null],
        ['framework' => 'PCI-DSS', 'code' => '7.2.1', 'title' => 'Access to System Components Limited', 'description' => 'An access control model is defined and applied to system components.', 'ml_level' => null],
        ['framework' => 'PCI-DSS', 'code' => '8.2.1', 'title' => 'Strong Authentication for Users', 'description' => 'Strong authentication for all users is established and managed.', 'ml_level' => null],
        ['framework' => 'PCI-DSS', 'code' => '8.3.1', 'title' => 'MFA for Access to CDE', 'description' => 'Multi-factor authentication (MFA) is implemented for all access into the cardholder data environment (CDE).', 'ml_level' => null],
        ['framework' => 'PCI-DSS', 'code' => '10.2.1', 'title' => 'Audit Logs Capture User Activities', 'description' => 'Audit logs are implemented to support anomaly detection and forensic investigations.', 'ml_level' => null],
        ['framework' => 'PCI-DSS', 'code' => '11.3.1', 'title' => 'Vulnerabilities Identified and Addressed', 'description' => 'Internal and external vulnerabilities are regularly identified, prioritized, and addressed.', 'ml_level' => null],
        ['framework' => 'PCI-DSS', 'code' => '12.1.1', 'title' => 'Overall Information Security Policy', 'description' => 'An overall information security policy is established and published.', 'ml_level' => null],

        // ===== SOC 2 (Trust Services Criteria) =====
        ['framework' => 'SOC2', 'code' => 'CC1.1', 'title' => 'Control Environment - Integrity and Ethical Values', 'description' => 'The entity demonstrates a commitment to integrity and ethical values.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC1.2', 'title' => 'Board Independence and Oversight', 'description' => 'The board of directors demonstrates independence from management and exercises oversight.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC1.3', 'title' => 'Management Establishes Structures', 'description' => 'Management establishes structures, reporting lines, and appropriate authorities.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC1.4', 'title' => 'Commitment to Competence', 'description' => 'The entity demonstrates a commitment to attract, develop, and retain competent individuals.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC1.5', 'title' => 'Enforces Accountability', 'description' => 'The entity holds individuals accountable for their internal control responsibilities.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC2.1', 'title' => 'Communication of Information', 'description' => 'The entity obtains or generates and uses relevant, quality information to support control functioning.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC3.1', 'title' => 'Risk Assessment Process', 'description' => 'The entity specifies objectives with sufficient clarity to enable identification of risks.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC3.2', 'title' => 'Risk Identification', 'description' => 'The entity identifies risks to the achievement of its objectives and analyzes risks.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC3.3', 'title' => 'Fraud Risk Assessment', 'description' => 'The entity considers the potential for fraud in assessing risks.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC3.4', 'title' => 'Identification of Changes', 'description' => 'The entity identifies and assesses changes that could significantly impact the system of internal control.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC6.1', 'title' => 'Logical and Physical Access Controls', 'description' => 'The entity implements logical access security software, infrastructure, and architectures.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC6.2', 'title' => 'Prior to Access, Identification and Authentication', 'description' => 'The entity identifies and authenticates users and processes prior to issuing access credentials.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC6.3', 'title' => 'Removal or Modification of Access Rights', 'description' => 'The entity removes access when an individual no longer requires access.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC6.6', 'title' => 'Transmission Encryption', 'description' => 'The entity implements encryption to protect data during transmission.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC6.7', 'title' => 'Data at Rest Encryption', 'description' => 'The entity restricts access to sensitive information.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC7.1', 'title' => 'Vulnerability Detection and Monitoring', 'description' => 'The entity uses detection and monitoring procedures to identify anomalies.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC7.2', 'title' => 'Security Incident Response', 'description' => 'The entity responds to identified security incidents.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC8.1', 'title' => 'Change Management', 'description' => 'The entity authorizes, designs, develops, configures, documents, tests, approves, and implements changes.', 'ml_level' => null],
        ['framework' => 'SOC2', 'code' => 'CC9.1', 'title' => 'Vendor and Business Partner Management', 'description' => 'The entity identifies, selects, and manages vendors and business partners.', 'ml_level' => null],

        // ===== ISO 27001:2022 (Annex A Controls - Sample) =====
        ['framework' => 'ISO27001', 'code' => 'A.5.1', 'title' => 'Policies for Information Security', 'description' => 'Information security policy and topic-specific policies shall be defined, approved by management, published, communicated and acknowledged.', 'ml_level' => null],
        ['framework' => 'ISO27001', 'code' => 'A.5.2', 'title' => 'Information Security Roles and Responsibilities', 'description' => 'Information security roles and responsibilities shall be defined and allocated.', 'ml_level' => null],
        ['framework' => 'ISO27001', 'code' => 'A.5.3', 'title' => 'Segregation of Duties', 'description' => 'Conflicting duties and conflicting areas of responsibility shall be segregated.', 'ml_level' => null],
        ['framework' => 'ISO27001', 'code' => 'A.5.7', 'title' => 'Threat Intelligence', 'description' => 'Information relating to information security threats shall be collected and analyzed.', 'ml_level' => null],
        ['framework' => 'ISO27001', 'code' => 'A.5.8', 'title' => 'Information Security in Project Management', 'description' => 'Information security shall be integrated into project management.', 'ml_level' => null],
        ['framework' => 'ISO27001', 'code' => 'A.5.9', 'title' => 'Inventory of Information and Other Associated Assets', 'description' => 'An inventory of information and other associated assets shall be developed and maintained.', 'ml_level' => null],
        ['framework' => 'ISO27001', 'code' => 'A.5.10', 'title' => 'Acceptable Use of Information and Other Associated Assets', 'description' => 'Rules for the acceptable use and procedures for handling information shall be identified, documented and implemented.', 'ml_level' => null],
        ['framework' => 'ISO27001', 'code' => 'A.5.15', 'title' => 'Access Control', 'description' => 'Rules to control physical and logical access to information and other associated assets shall be established and implemented.', 'ml_level' => null],
        ['framework' => 'ISO27001', 'code' => 'A.5.16', 'title' => 'Identity Management', 'description' => 'The full life cycle of identities shall be managed.', 'ml_level' => null],
        ['framework' => 'ISO27001', 'code' => 'A.5.17', 'title' => 'Authentication Information', 'description' => 'Allocation and management of authentication information shall be controlled by a management process.', 'ml_level' => null],
        ['framework' => 'ISO27001', 'code' => 'A.5.23', 'title' => 'Information Security for Use of Cloud Services', 'description' => 'Processes for acquisition, use, management and exit from cloud services shall be established.', 'ml_level' => null],
        ['framework' => 'ISO27001', 'code' => 'A.8.1', 'title' => 'User Endpoint Devices', 'description' => 'Information stored on, processed by or accessible via user endpoint devices shall be protected.', 'ml_level' => null],
        ['framework' => 'ISO27001', 'code' => 'A.8.2', 'title' => 'Privileged Access Rights', 'description' => 'The allocation and use of privileged access rights shall be restricted and managed.', 'ml_level' => null],
        ['framework' => 'ISO27001', 'code' => 'A.8.3', 'title' => 'Information Access Restriction', 'description' => 'Access to information and other associated assets shall be restricted in accordance with the access control policy.', 'ml_level' => null],
        ['framework' => 'ISO27001', 'code' => 'A.8.5', 'title' => 'Secure Authentication', 'description' => 'Secure authentication technologies and procedures shall be implemented based on information access restrictions.', 'ml_level' => null],
        ['framework' => 'ISO27001', 'code' => 'A.8.10', 'title' => 'Information Deletion', 'description' => 'Information stored in information systems, devices or in any other storage media shall be deleted when no longer required.', 'ml_level' => null],
        ['framework' => 'ISO27001', 'code' => 'A.8.23', 'title' => 'Web Filtering', 'description' => 'Access to external websites shall be managed to reduce exposure to malicious content.', 'ml_level' => null],
        ['framework' => 'ISO27001', 'code' => 'A.8.24', 'title' => 'Use of Cryptography', 'description' => 'Rules for the effective use of cryptography shall be defined and implemented.', 'ml_level' => null],
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
